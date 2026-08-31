<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_lessonbank;

/**
 * Makes the session cookie usable inside a cross site iframe.
 *
 * The lesson bank is browsed from other Moodle sites, which preview a lesson by embedding
 * mod_minilesson's view.php in an iframe. Moodle core issues its session cookie as SameSite=Lax
 * (\core\session\manager::init_session_storage(), which only uses None for the mobile app), and a
 * Lax cookie is not sent for a document inside a cross site iframe nor for anything that document
 * requests. The preview therefore renders - require_login() logs the visitor in as a guest without
 * needing a cookie - and then dies on its first web service call with "servicerequireslogin",
 * which lib/amd/src/ajax.js turns into a redirect of the iframe to the login page.
 *
 * Re-issuing the cookie as SameSite=None with the Partitioned (CHIPS) attribute fixes that. The
 * partition key is the embedding site, so the preview gets its own throwaway guest session per
 * embedder and a first party lesson bank session is never exposed cross site.
 *
 * Only requests that are actually in a third party context are touched, so ordinary first party
 * browsing of the lesson bank keeps core's Lax cookie.
 *
 * @package    local_lessonbank
 * @copyright  2026 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class embed_cookie {
    /**
     * Arrange for the session cookie on this request to be rewritten, if it needs to be.
     *
     * The rewrite has to happen at the last possible moment: the Set-Cookie header is queued when
     * the session starts, but Moodle can still regenerate the session id afterwards (logging the
     * visitor in as a guest does exactly that) and would then queue a fresh Lax header over the
     * top. header_register_callback() runs immediately before the headers are sent, which is after
     * anything that could re-issue the cookie.
     */
    public static function register(): void {
        if (CLI_SCRIPT || !self::is_wanted()) {
            return;
        }
        header_register_callback([self::class, 'rewrite_session_cookie']);
    }

    /**
     * Should this request's session cookie be made iframe friendly?
     *
     * @return bool
     */
    protected static function is_wanted(): bool {
        if (!get_config('local_lessonbank', 'embedcookie')) {
            return false;
        }

        // Partitioned requires a Secure cookie, which requires https.
        if (!is_https()) {
            return false;
        }

        // Fetch metadata tells us whether we are in a third party context. The iframe holding the
        // preview is the request that actually needs to store the cookie; everything the embedded
        // document goes on to request sends the stored cookie back rather than setting a new one.
        $dest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
        $site = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? '';
        return $dest === 'iframe' || $site === 'cross-site';
    }

    /**
     * Re-emit any session cookie queued on this response as SameSite=None; Secure; Partitioned.
     *
     * Other cookies on the response are passed through untouched.
     */
    public static function rewrite_session_cookie(): void {
        $sessionname = session_name();
        if (empty($sessionname)) {
            return;
        }

        $cookies = [];
        foreach (headers_list() as $header) {
            if (stripos($header, 'set-cookie:') === 0) {
                $cookies[] = trim(substr($header, strlen('set-cookie:')));
            }
        }
        if (empty($cookies)) {
            return;
        }

        $changed = false;
        foreach ($cookies as $i => $cookie) {
            if (stripos($cookie, $sessionname . '=') !== 0) {
                continue;
            }
            // Drop whatever core set so the attributes cannot end up duplicated, then add ours.
            $cookie = preg_replace('/;\s*SameSite\s*=[^;]*/i', '', $cookie);
            $cookie = preg_replace('/;\s*Partitioned/i', '', $cookie);
            if (!preg_match('/;\s*Secure\b/i', $cookie)) {
                $cookie .= '; Secure';
            }
            $cookies[$i] = $cookie . '; SameSite=None; Partitioned';
            $changed = true;
        }
        if (!$changed) {
            return;
        }

        header_remove('Set-Cookie');
        foreach ($cookies as $cookie) {
            header('Set-Cookie: ' . $cookie, false);
        }
    }
}
