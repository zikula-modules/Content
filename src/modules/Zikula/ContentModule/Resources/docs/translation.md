# TRANSLATION INSTRUCTIONS

To create a new translation follow the steps below:

1. First install the module like described in the `install.md` file.
2. Open a console and navigate to the Zikula root directory.
3. Execute this command replacing `en` by your desired locale code:

`php -dmemory_limit=2G bin/console translation:extract en --bundle=ZikulaContentModule --enable-extractor=jms_i18n_routing --output-format=po --exclude-dir=vendor`

You can also use multiple locales at once, for example `de fr es`.

4. Translate the resulting `.po` files in `modules/Zikula/ContentModule/Resources/translations/` using your favourite Gettext tooling.

Note you can even include custom views in `app/Resources/ZikulaContentModule/views/` and JavaScript files in `app/Resources/ZikulaContentModule/public/js/` like this:

`php -dmemory_limit=2G bin/console translation:extract en --bundle=ZikulaContentModule --enable-extractor=jms_i18n_routing --output-format=po --exclude-dir=vendor --dir=./modules/Zikula/ContentModule --dir=./app/Resources/ZikulaContentModule`

For questions and other remarks visit our homepage https://zikula.de.

Axel Guckelsberger (vorstand@zikula.de)
https://zikula.de
