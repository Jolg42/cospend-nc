# Проект Cospend для Nextcloud

Nextcloud Cospend - это менеджер группового/общего бюджета. Он был создан под впечатлением от отличной программы [IHateMoney](https://github.com/spiral-project/ihatemoney/).

Он пригодится, когда вы вскладчину снимаете жильё, или когда едете в отпуск с друзьями, в случаях, когда вы скидываетесь на что-либо.

Он позволяет вам создавать проекты в которых есть участники и счета. Баланс каждого участника вычисляется на основании заведенных в проект счетов. Таки образом сразу видно кто должен всей группе, а кому должна группа. В завершение проекта можно вычислить план расчетов по платежам для обнуления балансов участников.

Участники проекта не связаны с пользователями Nextcloud. Доступ к проектам и к редактированию их данных могут иметь люди и без регистрации в Nextcloud. У каждого проекта есть уникальный идентификатор и пароль для входа с гостевым доступом.

Клиент [MoneyBuster](https://gitlab.com/eneiluj/moneybuster) для Android [ доступен в F-Droid](https://f-droid.org/packages/net.eneiluj.moneybuster/) и в [Play store](https://play.google.com/store/apps/details?id=net.eneiluj.moneybuster).

Клиент для iOS [PayForMe](https://github.com/mayflower/PayForMe) в настоящее время разрабатывается!

## Features

* создание/редактирование/удаление проектов, участников, счетов, категорий счетов, валют
* ⚖ проверка балансов участников
* 🗠 отображение статистики проекта
* ♻ план расчета
* 🎇 автоматическое создание счетов на возмещение из плана расчёта
* 🗓 создание повторяющихся счетов (ежедневно/еженедельно/ежемесячно/ежегодно)
* 📊 возможность устрановить произвольную сумму для каждого участника во вводимых счетах
* 🔗 Прикрепление к счету личных файлов (например, фото физического счета)
* 👩 гостевой доступ вне Nextcloud
* 👫 разделение проекта с другими пользователями/группами/кругами Nextcloud
* 🖫 Импорт/экспорт проектов в формате csv (совместим с csv файлами из IHateMoney)
* 🔗 создание ссылки/QRCode для легкого импорта проектов в MoneyBuster
* реализация потока уведомлений и активности Nextcloud

Это приложение протестировано на Nextcloud 20+ с Firefox 57+ и Chromium.

Это приложение находится в стадии разработки.

🌍 Помогите перевести это приложение на [PhoneTrack Crowdin project](https://crowdin.com/project/moneybuster).

⚒ Посмотрите другие возможности для помощи проекту в [contribution guidelines](https://gitlab.com/eneiluj/cospend-nc/blob/master/CONTRIBUTING.md).

## Документация

* [Пользовательская документация](https://github.com/eneiluj/cospend-nc/blob/master/docs/user.md)
* [Документация по админу](https://github.com/eneiluj/cospend-nc/blob/master/docs/admin.md)
* [Документация для разработчиков](https://github.com/eneiluj/cospend-nc/blob/master/docs/dev.md)
* [CHANGELOG](https://github.com/eneiluj/cospend-nc/blob/master/CHANGELOG.md#change-log)
* [АВТОРЫ](https://github.com/eneiluj/cospend-nc/blob/master/AUTHORS.md#authors)

## Известные проблемы

* он не делает вас богатым

Мы будем признательны за любую обратную связь.

