# PHP Код для бизнес процесса "Заявление на командировку"



## Ознакомление

В бизнес процессе "Заявление на командировку" для реализации некоторого функционала используется PHP код. Далее будет описываться все методы и приемы на различных этапах бизнес процесса.

Бизнес процесс "Заявление на командировку" со статусами, в каждая директория обозначает статус где используется PHP код.

Большая часть методов в скрипта однотипные, их расшифровка хорошо описана в документации BitrixFramework на нее будут ссылки.

>Для корректоной работы всех скриптов в бизнес процессе, на портале Битрикс24 должна быть установлена версия PHP не ниже **8.1**

>Вставлять скрипты в блок бизнес процессов "PHP код" нужно без ```<?php```.

## Содержание

- [Основные методы в БП Битрикс24](#основные-методы-в-бп-битрикс24)
- [Запрос данных - директория begin](#запрос-данных-директория-begin)
- [Подтверждение данных - директория confirm_data](#подтверждение-данных-директория-confirmdata)
- [Подготовка к выезду - дирректория prepare_to_drive](#подготовка-к-выезду-дирректория-preparetodrive)
- [В командировке - директория in_drive](#в-командировке-директория-indrive)
- [Контроль начислений - директория pay_control](#контроль-начислений-директория-paycontrol)

***

## Основные методы в БП Битрикс24

Опишем основные методы PHP используемы в бизнес процессах Битрикс24:

#### Код для получения поля документа из бизнес процесса
```
$documentService = $this->workflow->GetService("DocumentService");
$document = $documentService->getDocument($this->getDocumentId());
$fieldValue = $document['PROPERTY_DOCUMENT_FIELD'];
```
Переменной ```$fieidValue``` присваивается значение поля документа. Название поля берем из массива ```$document[]```
где ```PROPERTY_DOCUMENT_FIELD``` - код поля БП с добавлением ```PROPERTY_```, иначе не работает.

#### Код для получения переменной бизнес процесса
```
$rootActivity = $this->GetRootActivity();
$groupID = $rootActivity->GetVariable("TasksGroupID");
```
Переменной ```$groupID``` присваивается значение переменной бизнес процесса. Метод ```GetVariable("TasksGroupID")``` возвращает значение переменной БП,
где ```TasksGroupID``` - код переменной БП.

#### Код для выгрузки данных из PHP в бизнес процесс
```
$this->SetVariable('Variable1', $var1);
```
Метод использует два аргумента первый - имя код переменной бизнес процесса в ковычках, второй - переменная PHP 
```SetVariable('{пременнаяБП}', ${переменнаяPHP})```.

>Между этими конструкциями производятся операции методами PHP.

#### Namespace
[Namespace](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=3524) используются в php фрейм ворках для быстрого доступа к классам и их методам. Очень важно использовать namespace, для того чтобы обратиться к классам Битрикса. 
Использует конструкцию в начале файла до вызова методов классов.
```
use \Bitrix\Main\Loader;
```

#### Подключение модулей
Важный метод BitrixFramework для подключения модулей [Loader::includeModule()](https://dev.1c-bitrix.ru/api_d7/bitrix/main/loader/includemodule.php) Необходим для использования классов и методов модулей. Используется в конструкции с ```if else```:
```
if Loader::includeModule('crm')) 
{
  // Пишем свой код
}
```

#### Глобальные переменные
Глобальные переменные ```$USER```, ```$APPLICATION``` зарезервированные переменные php. Инициализируются в начале скрипта, ```$USER``` - текущий пользователь, ```$APPLICATION``` - страница сайта. Пример использования:
```
global $USER;
global $APPLICATION;
```
>Инициализация переменных в скриптах БП в теории не обязательна по тому что эти переменные инициализируются в коде системы, но лучше перестраховаться.

***

## Запрос данных директория begin

В начале бизнес процесса обрабатываются первичные данные введенные пользователем.

### [count_user.php](begin/count_users.php)  
Получает массив ID пользователей из поля ```PROPERTY_SOTRUDNIKI_NAPRAVLENNYE_V_KOMANDIROVKU```.
Считает колличество прикрепленных пользователей к командировке с помощью функции php [count()](https://www.php.net/manual/ru/function.count.php), переменную ```$var1``` приводим к числовому типу данных ```intval($var1)``` и вводим результат в переменную БП ```HowManyPeopleOnTrip``` для дальнейшего расчета средств для проживания сотрудников направленных в командировку.

>В папке [for_market_select_contact](begin/for_market_select_contact) находятся файлы скриптов для ветки с видом командировки "Маркетинговый выезд к клиенту"

### [how_many_company_in_contact.php](begin/for_market_select_contact/how_many_company_in_contact.php) 
Получит переменную БП с ID контакта которые внес пользователь в заданий БП "Выберите контакты командировки". Скрипт получает компании которые привязаные к контакту c помощью метода BitrixFramework [ContactCompanyTable::getList()](https://dev.1c-bitrix.ru/api_d7/bitrix/crm/binding/contactcompanytable/index.php) и присваевает результат выполнения в переменную ```$arCompanyID```, далее в условии производится расчет колличества елементов (компаний у контакта) в массиве с помошью php функции [count()](https://www.php.net/manual/ru/function.count.php). Если в контакте несколько компаниий то возвращает в БП переменную ```ManyCompanyInContact``` булиновое значение ```true```, иначе возвращает ```false```. 

```
$arCompanyID = ContactCompanyTable::getList([
        'filter' => ['CONTACT_ID' => $contactID],
        'select' => ['COMPANY_ID'],
    ])->fetchAll();
```
Конструкция использования метода [getList()](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=5753), в ```filter``` указываем ID контакта (в нем будем искать), в ```select``` указываем что будем искать ID компании.
> Данный скрипт необходим для дальнейшей проверки в БП что бы отработать моменты когда к контакту не прикреплены компании или компаний несколько. 

Если компаний несколько, то процесс идет по следующим скриптам в их номерной последовательности: 

1. [If_many_company.php](#ifcompanynotfoundphp) 
2. [after_if_many_company.php](#afterifmanycompanyphp)
3. [check_many_company_in_contact.php](#checkmanycompanyincontactphp)

Если компания одна или контакт не закреплен ни за одной компанией запускается скрипт [if_few_company.php](#iffewcompanyphp). Далее процесс проходит через условие БП в котором определяется наличие компании. В случает отсутствия компании, запрашивается доп информации в задании БП "Вы указали в контакт который не закреплен ни за одной компанией" и запускается скрипт [if_company_not_found.php](#ifcompanynotfoundphp).

### [If_many_company.php](begin/for_market_select_contact/If_many_company.php)

Получает переменную БП ```Contact_ID``` с ID контакта, который ввел пользователь. Получаем контакт по его ID с помощью метода ```ContactTable::getById()```. [getById()](https://dev.1c-bitrix.ru/api_help/main/reference/cuser/getbyid.php) Универсальный метод для получения данных сущности по ее ID используем функцию ```fetch()```. На выходе получаем массив с данными о контакте.
```
$contact = ContactTable::getById($contactID)->fetch();
```
Формируем ссылку на контакт с помощью [BB-code Битрикс24](https://helpdesk.bitrix24.ru/open/6060589/) в переменную ```$contactUrl``` 

Получаем компании методом ```ContactCompanyTable::getList()```:
```
$arCompanyID = ContactCompanyTable::getList([
        'filter' => ['CONTACT_ID' => $contactID],
        'select' => ['COMPANY_ID'],
    ])->fetchAll();
```
Формируем ссылку на компанию, и так как на выходе из предыдущей операции на пришел массив перебираем в цикле ```foreach()```:
```
    foreach ($arCompanyID as $id) {
        $company = CompanyTable::getById($id['COMPANY_ID'])->fetch();
        $companyName = $company['TITLE'];
        $companyUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/company/details/'.$company['ID'].'/]'.'*'.'[/url]';
        $companyIDs[] = $id['COMPANY_ID'];

        $companyListByContact[] = $companyName." ".$companyUrl;
    }
```
Из скрипта должны вывести в БП переменные: ```$companyListByContact -> CompanyNameIfMany``` ```$contactUrl -> ContactNameIfMany``` ```$companyIDs - > CompanyListByContact```.

>Скрипт выполняется в цикле БП, по этому перебор массива (парсинг) входящих данных не требуется.

### [after_if_many_company.php](begin/for_market_select_contact/after_if_many_company.php)
Скрипт необходим для отработки ситуации с несколькими компаниями и выполняется после того как пользователь выберет нужную компанию в задании бизнес процесса.

Получает переменную БП ```Contact_ID``` с ID контакта и переменную БП ```CompanyListByContact```.
Дальнейшие действия аналогичны со скриптом [If_many_company.php](#ifmanycompanyphp), отличаются только тем что ID компании получать не требуется так как ID приходит к нам из БП и присваивается в переменную ```$companyID```.

### [check_many_company_in_contact.php](begin/for_market_select_contact/check_many_company_in_contact.php)
В скрипте производится проверка на количество ID компаний в массиве приходящем из переменной БП ```CompanyListByContact```. Необходим для отработки ситуации когда пользователь оставил в поле выбора компаний 2 или более компаний.
Выполняется следующим образом:
```
if (count($arCompanyIDs) > 1) {
    $companyMany = true;
} else {
    $companyMany = false;
}
```
Значение ```$companyMany``` выводим в БП в переменную ```ManyCompanyInContact```.

### [if_few_company.php](begin/for_market_select_contact/if_few_company.php)
Скрипт запускается при условии если компания в контакте 1 или контакт вовсе не привязан ни к одной компании.

Получает переменную БП ```Contact_ID```, далее производит операции по поиску ID компаний и формированию ссылок на контакт и компанию.

Получаем массив контактов и ID привязанной компании, формируем ссылку на контакт помощью [BB-code Битрикс24](https://helpdesk.bitrix24.ru/open/6060589/):
```
if (Loader::includeModule('crm')) { // подключем модуль CRM для использования его функций
    $contact = ContactTable::getById($contactID)->fetch(); // получаем контакт по его ID
    $contactName = $contact['FULL_NAME'];
    $companyID = $contact['COMPANY_ID']; // получаем ID компании
    $contactUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/contact/details/'.$contactID.'/]'.$contactName.'[/url]'; // формируем ссылку
```
Для компании аналогично за исключением того что необходимо проверить наличие компании, если ее нет то в переменной ```$companyNotFound``` присваивать значение ```true```:

```
if ($companyID) {
        $company = CompanyTable::getById($companyID)->fetch();
        $companyName = $company['TITLE'];
        $companyUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/company/details/'.$companyID.'/]'.'*'.'[/url]';
    } else {
        $companyNotFound = true;
        $companyName = "Компания не найдена";
        $companyUrl = '*';
    }
```
В переменную ```$finalListCC``` присваиваются все данные которые собирались по ходу скрипта. И она же формирует список для отображения пользователю.

```
$finalListCC = "\n".$contactUrl.": ".$companyName." ".$companyUrl;
```

### [if_company_not_found.php](begin/for_market_select_contact/if_company_not_found.php)
Скрипт запускается при условии если контакт не привязан к ник одной компании, после задания БП в котором пользователь должен выбрать или создать компанию для этого контакта.

Получает имя контакта и формирует ссылку на него из переменной БП ```Contact_ID``` по ID:
```
if (Loader::includeModule('crm')) {
    $contact = ContactTable::getById($contactID)->fetch();
    $contactName = $contact['FULL_NAME'];
    $contactUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/contact/details/'.$contactID.'/]'.$contactName.'[/url]';
```

Из переменной ```CompanyListByContactAdd``` получает ID компании и формирует ссылку на нее. Так как тип полученных данных из переменной ```CompanyListByContactAdd``` - массив, операции с компанией производятся в цикле ```foreach```:
```
foreach ($companyID as $id) {
        if ($id) {
            $companyNotFound = false;
            $company = CompanyTable::getById($id)->fetch();
            $companyName = $company['TITLE'];
            $companyUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/company/details/'.$id.'/]'.'*'.'[/url]';
        } else {
            $companyNotFound = true;
        }
    }
```
Далее формируется список для отображения пользователю в переменную ```$finalListCC```.
```
$finalListCC = "\n".$contactUrl.": ".$companyName." ".$companyUrl;
```

***

>В папке [for_not_market_select_tasks](begin/for_not_market_select_tasks) находятся файлы скриптов для ветки с видом командировки "Монтаж/Сервис"

### [get_tasks_list.php](begin/for_not_market_select_tasks/get_tasks_list.php)
Скрипт запускается перед получением пользователем задания БП "Укажите задачи командировки" и подготавливает список активных актуальных задач, где пользователь ответственный.
Присваиваем значение переменной БП ```TasksGroupID``` в переменную ```$groupID```. Необходимо для определения группы задач для сотрудников.
Получаем текущего пользователя методом [$USER->GetID()](https://dev.1c-bitrix.ru/api_help/main/reference/cuser/getid.php). 

Подключаем модуль задач и находим все актуальные задачи пользователя в группе ID которых находятся в переменной ```$groupID```:
```
if (\Bitrix\Main\Loader::includeModule("tasks"))
{
    $result = CTasks::GetList(
        array("TITLE" => "ASC"),
        array("RESPONSIBLE_ID" => "$author_ID",
            'REAL_STATUS' => array(CTasks::STATE_NEW, CTasks::STATE_PENDING, CTasks::STATE_IN_PROGRESS),
            'GROUP_ID' => $groupID),
    );
}
```
Перебираем результаты выполнения предыдущего блока кода и формируем ссылки на задачи:
```
while ($arTask = $result->GetNext())
{
    $arTaskUrl[] = "\n".$arTask['ID']." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]';
    $arTaskId[] = $arTask['ID'];
}
```
Предаем значения переменных в бизнес процесс.

### [set_tasks_list_in_document.php](begin/for_not_market_select_tasks/set_tasks_list_in_document.php)

Скрипт запускается для обработки значений которые пользователь ввел в задании бизнес процесса. Вытягивает из выбранных задач компании и формирует ссылки на задачи и компании.

Перебираем ID задач полученных и значения переменной БП ```TasksNumbers``` в цикле ```foreach``` и формируем ссылки на задачи:
```
foreach ($TasksId as $id)
{
    $resTasks = CTasks::GetByID($id);
    if ($arTask = $resTasks->GetNext()) {
        $arTaskUrl[] = "\n".$id." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/url]';
        // условие выборки сущностей CRM
    }
}
```
Определяем привязаны ли к задаче сущности CRM и определяем компанию, к ней формируем ссылку и отрабатываем случай когда сущностей не было найдено:
```
if ($arTask['UF_CRM_TASK']) 
{
    foreach ($arTask['UF_CRM_TASK'] as $idEl) 
    {
        if (str_starts_with($idEl, 'CO')){
            $arCompanyInTasksId[] = $idEl;
            $idComp = ltrim($idEl, "CO_");
            if (Loader::includeModule('crm'))
            {
                $company = CompanyTable::getById($idComp,['select' => array("ID", "TITLE")])->fetch();
                $taskURL = "\n".$id." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/url]';
                $companyTitle = $company["TITLE"];
                $finalListTaskAndCompany[] = $taskURL." | ".$companyTitle;
            }
        }
    }
} else {
  $taskURL = "\n".$id." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/url]';
  $finalListTaskAndCompany[] = $taskURL." | "."Компания не найдена";
}
```
В коде описаны методы которые приводят значения поля ```UF_CRM_TASK``` к виду стандартного ID (целое число), так как записи о сущностях в поле хранятся в виде ```ТИПСУЩНОСТИ_IDСУЩНОСТИ -> CO_12345```. Результат удалось получить с помощью функций php [str_starts_with()](https://www.php.net/manual/ru/function.str-starts-with.php) - находит в массиве запись с параметром и [ltrim()](https://www.php.net/manual/ru/function.ltrim.php) - обрезает нужные символы из строки.
***

## Подтверждение данных директория confirm_data
Используется всего один скрипт [get_number_rn.php](confirm_data/get_number_rn.php).

Скрипт вытягивает из названия задачи номер РН необходимы для дальнейшего использования в 1С. 

По ходу выполнения подключает модуль CRM, обрезает полученную строку из поля ```TITLE``` функцией [substr()](https://www.php.net/manual/ru/function.substr.php) c 1 до 16 символа ```substr($arTask['TITLE'], 0, 15)```, обрабатываем полученную строку с помощью регулярного выражения и функции [preg_replace()](https://www.php.net/manual/en/function.preg-replace.php) оставляя только цифры ```preg_replace("/[^0-9.,]/", "", $numRN)```:
```
if (Loader::includeModule("tasks")) 
{
    $resTasks = CTasks::GetByID($task_ID);
    if ($arTask = $resTasks->GetNext()) {
        $numRN = substr($arTask['TITLE'], 0, 15);
        $numberRN = preg_replace("/[^0-9.,]/", "", $numRN);
    }
}
```

***

## Подготовка к выезду директория prepare_to_drive
Используется всего один скрипт [get_number_rn.php](confirm_data/get_number_rn.php).

Получаем ID настоящего пользователя:
```
$authorID = $USER->GetID()
```
Получаем все данные по пользователю:
```
$rsUser = Cuser::GetByID($authorID);
$arUser = $rsUser->Fetch();
```
Получаем пользовательское поле "Организация":
```
$orgUser = $arUser['UF_USR_1695726543854']
```
Выводим ID организации в бизнес процесс, где происходит основная логика определения бухгалтера для пользователя.
***

## В командировке директория in_drive
>В статусе "В командировке" используются однотипные решения как в статусе ["Запрос данных"](#запрос-данных-директория-begin) с минимальными отличиями входных данных. 

#### Файлы директории:
Директория [for_market_select_contact_add](in_drive/for_market_select_contact_add)

[how_many_company_in_contact.php](in_drive/for_market_select_contact_add/how_many_company_in_contact.php)

[If_many_company.php](in_drive/for_market_select_contact_add/If_many_company.php)

[after_if_many_company.php](in_drive/for_market_select_contact_add/after_if_many_company.php)

[check_many_company_in_contact.php](in_drive/for_market_select_contact_add/check_many_company_in_contact.php)

[if_few_company.php](in_drive/for_market_select_contact_add/if_few_company.php)

[if_company_not_found.php](in_drive/for_market_select_contact_add/if_company_not_found.php)

Директория [for_not_market_select_task_add](in_drive/for_not_market_select_task_add)

[get_number_rn_not_market.php](in_drive/for_not_market_select_task_add/get_number_rn_not_market.php) (аналогичный скрипт [get_number_rn.php](confirm_data/get_number_rn.php))

В скрипте [get_tasks_list_add.php](in_drive/for_not_market_select_task_add/get_tasks_list_add.php) добавляется проверка на наличие задач добавленных раннее:
```
if (!in_array($arTask['ID'], $TasksId)) 
{
    $arTaskUrl[] = "\n".$arTask['ID']." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]';
    $arTaskId[] = $arTask['ID'];
} else {
    $arTaskUrl[] = "\n".$arTask['ID']." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]'." [I](Добавлены ранее)[/I]";
}
```

[set_tasks_list_in_document_add.php](in_drive/for_not_market_select_task_add/set_tasks_list_in_document_add.php)
***

## Контроль начислений директория pay_control
Скрипт получает и формирует все необходимые данные для задач "Контроль начислений" исходя из выбранных пользователем задач.

Получаем значение переменной БП ```TaskIDForPayControl``` (ID задачи) и настоящего пользователя ```$author_ID = $USER->GetID()```, подключаем модуль CRM и получаем данные задачу по ее ID,
далее получаем РН (см. [get_number_rn.php](confirm_data/get_number_rn.php)), получаем контакты и компании перебирая поле задачи ```UF_CRM_TASK``` через ```foreach```:

```
foreach ($arTask['UF_CRM_TASK'] as $idEl) {
    if (str_starts_with($idEl, 'CO')) {
        $arCompany_ID[] = $idEl;
        $idComp = ltrim($idEl, "CO_");
        if (Loader::includeModule('crm')){
            $company = CompanyTable::getById($idComp,['select' => array("ID", "TITLE")])->fetch();
            $companyTitle[] = $company["TITLE"];
        }
    } elseif (str_starts_with($idEl, 'C_')) {
        $arContact_ID[] = $idEl;
    } else {
        $arOther_IDel[] = $idEl;
    }
}
```

***

## Авторы
Разработчик: Павленко Дмитрий

E-mail: [pavlenkodim@mail.ru](mailto:pavlenkodim@mail.ru)

Телефон: [+7 (705) 559-81-46](tel:+77055598146) 

## Статус проекта
Введен в эксплуатацию.

Поддерживается и добавляется новый функционал.

## Лицензия

Этот проект распространяется под [лицензией MIT](https://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_MIT). Мы приглашаем вас к участию и предлагаем внести свой вклад!