Include If Exists compiler plugin for Smarty v3
=====

Summary
---

This is a smarty v3 plugin for specifying conditional inclusion of subtemplates within smarty templates.

[For a similar plugin for smarty v2 click here](http://code.google.com/p/smartyplugin-include-if-exists/)

Usage
---

Use the following syntax in smarty templates: `{include_if_exists file="foo.tpl" else="bar.tpl"}`

Installation
---

1. The easy way: copy the file compiler.include_if_exists.php in your smarty plugins directory.
2. The silky way:
    - put the file compiler.include_if_exists.php in an arbitrary folder
    - add the plugin dir to smarty upon initialization `$smarty->addPluginsDir("your/path/to/the/files");`
