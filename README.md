# moodle-local_lessonbank
A lesson bank for Poodll MiniLessons. 

This provides a web services interface for Poodll MiniLessons to search and import lessons from a remote LessonBank server. MiniLessons are created on the LessonBank server. Metadata, such as language level and a poster image for each lesson, is stored in custom fields at the activity level. There is no custom database/table of Minilesson data. The server simply searches the minilesson activities and their metadata, and uses the native MiniLesson export-to-json feature to provide the import file.

It would be possible to extend the LessonBank to work with other activities. It is also possible to create independant LessonBank servers for serving your own minilessons. (Would anyone do that? I guess that is why you are here ..) 

## Requirements
You will need Moodle 5.1 or greater and the following plugins:
* mod_minilesson - https://moodle.org/plugins/mod_minilesson
* local_modcustomfield - https://moodle.org/plugins/local_modcustomfields
* customfield_multiselect - https://moodle.org/plugins/customfield_multiselect
* customfield_picture - https://moodle.org/plugins/customfield_picture

## Setting up a LessonBank server
After installing everything, visit:
1. Site administration -> Plugins -> Local Plugins -> Custom Fields for Activity Modules -> Custom fields
2. There create a new custom field category called "Lesson bank"  NB it is case sensitive.
3. In the Lesson bank category create the following custom fields:

|Name              |Short Name        |Type                 |
|:-----------------|:-----------------|:--------------------|
|Poster Image      | posterimage      | picture             |
|Description       | description      | short text          |
|Version           | version          | short text          |
|Language Level    | languagelevel    | dropdown menu       |
|Skills            | skills           | multiselect menu    |
|Keywords          | keywords         | shorttext           |
|Key Vocabulary    | keyvocabulary    | shorttext           |

Thats it. Now create MiniLessons on the server. Make sure to put something in each of the Lesson bank custom fields in the activities. Set the LessonBank server url in the Poodll MiniLesson admin settings on the Moodle server(s) that will access the LessonBank server.