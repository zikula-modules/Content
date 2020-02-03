# Translation instructions

To create a new translation follow the steps below:

1. First install the module like described in the `install.md` file.
2. Open a console and navigate to the Zikula root directory.
3. Execute this command replacing `en` by your desired locale code:

`php -dmemory_limit=2G bin/console translation:extract --bundle=ZikulaContentModule extension en`

4. Translate the resulting `.yaml` files in `extensions/Zikula/ContentModule/Resources/translations/`.

For questions and other remarks visit our homepage <https://ziku.la>.

Axel Guckelsberger (info@ziku.la)
<https://ziku.la>
