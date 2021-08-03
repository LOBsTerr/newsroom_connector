# Newsroom Connector

Newsroom Connector allows you to be integrated with newsroom application.

### Configuration

To configure newsroom connector use **/admin/content/newsroom**

Normally, it is enough just set universe id, but of course it is possible to set additional settings to communicate with newsroom side.

In order to import content during the cron execution use "**Importer settings**", choose an importer whic should be executed on every cron execution

### Import

To see all available importers visit **/admin/content/newsroom/importers**

Take into account that some additional modules must be enabled to see more importers.
Check "modules" folder.

To import a single item use the next pattern

**/newsroom-import/[TYPE]/[NEWSROOM_ID]**

**TYPE** is a specific type of content, for example item, topic, type and etc. Check NewsroomProcessor plugin id.
all types have prefix "newsroom_", for example newsroom item has the plugin id newsroom_item.

**NEWSROOM_ID** is an original newsroom id, which we keep to keep connection between Drupal and Newsroom content.

Some examples:

* /newsroom-import/item/12345
* /newsroom-import/topic/12345
* /newsroom-import/type/12345

These URLs are used for ping mechanism.

### Redirection or public URL

to quickly find content item imported to a Drupal website we can use redirection URL

**/newsroom-redirect/[TYPE]/[NEWSROOM_ID]**

**TYPE** is a specific type of content, for example item, topic, type and etc. Check NewsroomProcessor plugin id.
All types have prefix "newsroom_", for example newsroom item has the plugin id "newsroom_item".

**NEWSROOM_ID** is an original newsroom id, which we keep to keep connection between Drupal and Newsroom content.

Some examples:

* /newsroom-redirect/item/12345
* /newsroom-redirect/topic/12345
* /newsroom-redirect/type/12345

### Define a new importer

Importers are attached to specific content types or taxonomies.

1) You need to add a content type / a taxonomy or custom content entity
2) Add all necessary fields, which will similar structure to the newsroom
3) Add migration, add translation migrations if it is required and add translations derivers. Check migrate_plus.migration.newsroom_item, migrate_plus.migration.newsroom_item_translations and NewsroomItemLanguageDeriver
4) Add Plugin NewsroomProcessor which will be picked up by the system. Check NewsroomItemNewsroomProcessor and NewsroomTopicNewsroomProcessor for examples

