[![Current version](https://img.shields.io/packagist/v/maatify/cron-email)][pkg]
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/maatify/cron-email)][pkg]
[![Monthly Downloads](https://img.shields.io/packagist/dm/maatify/cron-email)][pkg-stats]
[![Total Downloads](https://img.shields.io/packagist/dt/maatify/cron-email)][pkg-stats]
[![Stars](https://img.shields.io/packagist/stars/maatify/cron-email)](https://github.com/maatify/CronEmail/stargazers)

[pkg]: <https://packagist.org/packages/maatify/cron-email>
[pkg-stats]: <https://packagist.org/packages/maatify/routee/cron-email>
# Installation

```shell
composer require maatify/cron-email
```

## Database Structure
```mysql

--
-- Database: `maatify`
--

-- --------------------------------------------------------

--
-- Table structure for table `cron_telegram_bot`
--

CREATE TABLE `cron_telegram_bot` (
     `cron_id` int(11) NOT NULL,
     `type_id` int(11) NOT NULL DEFAULT '1' COMMENT '1=OTP; 2=Temp Password; 3=message',
     `recipient_id` int(11) NOT NULL DEFAULT '0',
     `recipient_type` varchar(64) NOT NULL DEFAULT '',
     `chat_id` int(11) NOT NULL DEFAULT '0',
     `message` text,
     `record_time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
     `status` tinyint(1) NOT NULL DEFAULT '0',
     `sent_time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- --------------------------------------------------------


--
-- Indexes for dumped tables
--

--
-- Indexes for table `cron_telegram_bot`
--
ALTER TABLE `cron_telegram_bot`
    ADD PRIMARY KEY (`cron_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cron_telegram_bot`
--
ALTER TABLE `cron_telegram_bot`
    MODIFY `cron_id` int(11) NOT NULL AUTO_INCREMENT;
```