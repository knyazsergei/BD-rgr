-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 10 2017 г., 12:03
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `rgr`
--

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `noteId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`id`, `image`, `noteId`) VALUES
(14, '5890e5e3d70d03215354534.jpg', 0),
(15, '5890e5e3d70dc3215442987.jpg', 0),
(16, '5890e5f418bd53241418053.jpg', 0),
(17, '5891a88ab71e73215442987.jpg', 0),
(18, '5891a88ab4f8d3241418053.jpg', 0),
(19, '5891a932a86777.jpg', 0),
(20, '5891a932aae128.jpg', 0),
(21, '5891a932c45ee9.jpg', 0),
(22, '5891a932d1cfb10.jpg', 0),
(23, '5891a932d683611.jpg', 0),
(24, '5891a932ece5c13.jpg', 0),
(25, '5891a932ed81012.jpg', 0),
(26, '589d66b511c3cphoto_2017-02-09_23-22-42.jpg', 0),
(27, '589d66b511c03photo_2017-02-09_23-26-28.jpg', 0),
(28, '589d66b53153bphoto_2017-02-09_23-26-31.jpg', 0),
(29, '589d672d99133rekl.png', 0),
(30, '589d698306cc8Screenshot_2.png', 38),
(31, '589d6bb9b2c4e3215442988.jpg', 38),
(32, '589d6bb9b3dcd3215442987.jpg', 38);

-- --------------------------------------------------------

--
-- Структура таблицы `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tages` varchar(255) NOT NULL,
  `video` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

--
-- Дамп данных таблицы `notes`
--

INSERT INTO `notes` (`id`, `title`, `description`, `tages`, `video`, `date`, `author_id`) VALUES
(38, 'hello world', 'First post\n', '', '', '2017-01-13', 16),
(39, 'Без названия', '', '1,2', '', '2017-01-29', 17),
(40, 'Без названия', '', '', '', '2017-01-29', 17),
(41, 'Без названия', '', '', '', '2017-01-29', 17),
(42, 'Без названия', '', '', '', '2017-01-29', 17),
(43, 'Без названия', '', '', '', '2017-01-29', 17),
(44, 'Без названия', '', '', '', '2017-01-29', 17),
(45, 'My Note Hello world', '', '1,2', '', '2017-01-31', 18);

-- --------------------------------------------------------

--
-- Структура таблицы `tages`
--

CREATE TABLE IF NOT EXISTS `tages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `tages`
--

INSERT INTO `tages` (`id`, `word`, `date`, `count`) VALUES
(1, 'hello', '2017-01-29', 0),
(2, 'world', '2017-01-29', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `email`, `hash`) VALUES
(16, 'admin', '14e1b600b1fd579f47433b88e8d85291', 'yaknyazsergei@yandex.ru', 'aa6ab17cd81608b0fbcf6502a985650e'),
(17, 'asdasd', '63ee451939ed580ef3c4b6f0109d1fd0', 'asdjlk@ya.ru', '24c15d489eb943393ef76abb1df88590'),
(18, 'admin1', '63ee451939ed580ef3c4b6f0109d1fd0', 'awad@ya.ru', 'caa838328db965194101fbcc4f7bb22f');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
