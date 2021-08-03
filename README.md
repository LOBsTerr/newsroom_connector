# Newsroom Connector

Newsroom Connector allows you to be integrated with newsroom application.

### Configuration

To configure newsroom connector use **/admin/content/newsroom**
Normally, it is enough just set universe id, but of course you can set additional settings to communicate with newsroom side.

In order to import content during the cron execution use "**Importer settings**", choose an importer whic should be executed on every cron execution

### Import

To see all available importers visit **/admin/content/newsroom/importers**

Take into account that you have to enable additional modules to see more importers.
Check "modules" folder.

To import a single item use the next pattern

**/newsroom-import/[TYPE]/[NEWSROOM_ID]**

**TYPE** is a specific type of content, for example item, topic, type and etc. Check NewsroomProcessor plugin id.
all types have prefix "newsroom_", for example newsroom item has the plugin id newsroom_item.

**NEWSROOM_ID** is original newsroom id, which we keep to keep connection between Drupal and Newsroom content.

Some examples

/newsroom-import/item/12345
/newsroom-import/topic/12345
/newsroom-import/type/12345

These URLs are used for ping mechanism.

### Redirection or public URL

to quickly find content imported to Drupal website we can use redirect URL

**/newsroom-redirect/[TYPE]/[NEWSROOM_ID]**

**TYPE** is a specific type of content, for example item, topic, type and etc. Check NewsroomProcessor plugin id.
all types have prefix "newsroom_", for example newsroom item has the plugin id newsroom_item.

**NEWSROOM_ID** is original newsroom id, which we keep to keep connection between Drupal and Newsroom content.
