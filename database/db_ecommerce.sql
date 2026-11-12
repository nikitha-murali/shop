-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2023 at 11:26 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `bags`
--

CREATE TABLE `bags` (
  `id` int(10) NOT NULL,
  `bags_category_name` varchar(50) NOT NULL,
  `bags_category_quantity` int(10) DEFAULT 0,
  `bags_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bags`
--

INSERT INTO `bags` (`id`, `bags_category_name`, `bags_category_quantity`, `bags_category_status`) VALUES
(1, 'کیف خرید', 62, 0x31),
(2, 'کیف دستی', 35, 0x31),
(3, 'کیف پول', 75, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `banner_id` int(11) NOT NULL,
  `banner_subtitle` varchar(50) NOT NULL,
  `banner_title` text NOT NULL,
  `banner_items_price` int(10) NOT NULL,
  `banner_image` varchar(50) NOT NULL,
  `banner_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`banner_id`, `banner_subtitle`, `banner_title`, `banner_items_price`, `banner_image`, `banner_status`) VALUES
(1, 'محصولات پرطرفدار', 'آخرین مد زنانه در حراج', 20, 'banner-1.jpg', 0x31),
(2, 'اکسسوری‌های پرطرفدار', 'عینک آفتابی مدرن', 15, 'banner-2.jpg', 0x31),
(3, 'پیشنهاد ویژه', 'حراج تابستانی مد جدید', 29, 'banner-3.jpg', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `img` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` binary(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `img`, `created_at`, `updated_at`, `status`) VALUES
(1, 'لباس', 'dress.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(2, 'کفش', 'shoes.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(3, 'جواهرات', 'jewelry.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(4, 'عطر', 'perfume.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(5, 'آرایشی', 'cosmetics.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(6, 'عینک', 'glasses.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(7, 'کیف', 'bag.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(8, 'الکترونیک', 'watch.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `category_bar`
--

CREATE TABLE `category_bar` (
  `id` int(10) NOT NULL,
  `category_title` varchar(50) NOT NULL,
  `category_quantity` int(10) NOT NULL,
  `category_img` varchar(50) NOT NULL,
  `category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_bar`
--

INSERT INTO `category_bar` (`id`, `category_title`, `category_quantity`, `category_img`, `category_status`) VALUES
(1, 'لباس و پیراهن', 53, 'coat.svg', 0x31),
(2, 'عینک و لنز', 68, 'glasses.svg', 0x31),
(3, 'شلوارک و شلوار جین', 84, 'shorts.svg', 0x31),
(4, 'تی‌شرت', 35, 'tee.svg', 0x31),
(5, 'کت', 16, 'jacket.svg', 0x31),
(6, 'ساعت', 27, 'watch.svg', 0x31),
(7, 'کلاه', 39, 'hat.svg', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `category_electronics`
--

CREATE TABLE `category_electronics` (
  `id` int(10) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `status` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_electronics`
--

INSERT INTO `category_electronics` (`id`, `category_name`, `status`) VALUES
(1, 'رایانه رومیزی', 1),
(2, 'لپ‌تاپ', 1),
(3, 'دوربین', 1),
(4, 'تبلت', 1),
(5, 'هدفون', 1);

-- --------------------------------------------------------

--
-- Table structure for table `outfits`
--

CREATE TABLE `outfits` (
  `id` int(10) NOT NULL,
  `outfits_category_name` varchar(50) NOT NULL,
  `outfits_category_quantity` int(10) DEFAULT 0,
  `coloth_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outfits`
--

INSERT INTO `outfits` (`id`, `outfits_category_name`, `outfits_category_quantity`, `coloth_category_status`) VALUES
(1, 'پیراهن', 300, 0x31),
(2, 'شلوارک و شلوار جین', 60, 0x31),
(4, 'کت', 50, 0x31),
(5, 'لباس و پیراهن', 87, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `cosmetics`
--

CREATE TABLE `cosmetics` (
  `id` int(10) NOT NULL,
  `cosmetics_category_name` varchar(50) NOT NULL,
  `cosmetics_category_quantity` int(10) DEFAULT 0,
  `cosmetics_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetics`
--

INSERT INTO `cosmetics` (`id`, `cosmetics_category_name`, `cosmetics_category_quantity`, `cosmetics_category_status`) VALUES
(1, 'شامپو', 68, 0x31),
(2, 'ضدآفتاب', 46, 0x31),
(3, 'شوینده بدن', 79, 0x31),
(4, 'کیت آرایش', 23, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(100) NOT NULL,
  `customer_fname` varchar(50) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_pwd` varchar(100) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_role` varchar(50) NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_fname`, `customer_email`, `customer_pwd`, `customer_phone`, `customer_address`, `customer_role`) VALUES
(9, 'فهاد ادمین', 'dev.shahfahad@gmail.com', 'adminfahad', '03469589557', 'پیشاور، پاکستان', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `deal_of_the_day`
--

CREATE TABLE `deal_of_the_day` (
  `deal_id` int(10) NOT NULL,
  `deal_title` text NOT NULL,
  `deal_description` text NOT NULL,
  `deal_net_price` double(10,2) NOT NULL,
  `deal_discounted_price` double(10,2) NOT NULL,
  `available_deal` int(10) NOT NULL,
  `sold_deal` int(10) NOT NULL,
  `deal_image` varchar(50) NOT NULL,
  `deal_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deal_of_the_day`
--

INSERT INTO `deal_of_the_day` (`deal_id`, `deal_title`, `deal_description`, `deal_net_price`, `deal_discounted_price`, `available_deal`, `sold_deal`, `deal_image`, `deal_status`) VALUES
(1, 'پک شامپو، نرم‌کننده و شوینده صورت', 'لورم ایپسوم دولور سیت آمت کنسکتتور لورم ایپسوم دولور سیت آمت کنسکتتور لورم ایپسوم دولور', 200.00, 150.00, 40, 20, 'shampoo.jpg', 0x31),
(2, 'گوشواره الماس رزگلد', 'لورم ایپسوم دولور سیت آمت کنسکتتور لورم ایپسوم دولور سیت آمت کنسکتتور لورم ایپسوم دولور', 250.00, 190.00, 40, 15, 'jewellery-1.jpg', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `footwear`
--

CREATE TABLE `footwear` (
  `id` int(10) NOT NULL,
  `footwear_category_name` varchar(50) NOT NULL,
  `footwear_category_quantity` int(10) DEFAULT 0,
  `footwear_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footwear`
--

INSERT INTO `footwear` (`id`, `footwear_category_name`, `footwear_category_quantity`, `footwear_category_status`) VALUES
(1, 'ورزشی', 45, 0x31),
(2, 'رسمی', 75, 0x31),
(3, 'کژوال', 35, 0x31),
(4, 'کت', 50, 0x31),
(5, 'کفش ایمنی', 26, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `glasses`
--

CREATE TABLE `glasses` (
  `id` int(10) NOT NULL,
  `glasses_category_name` varchar(50) NOT NULL,
  `glasses_category_quantity` int(10) DEFAULT 0,
  `glasses_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glasses`
--

INSERT INTO `glasses` (`id`, `glasses_category_name`, `glasses_category_quantity`, `glasses_category_status`) VALUES
(1, 'عینک آفتابی', 50, 0x31),
(2, 'لنز', 48, 0x31),
(3, 'لنز', 48, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `jewelry`
--

CREATE TABLE `jewelry` (
  `id` int(10) NOT NULL,
  `Jewelry_category_name` varchar(50) NOT NULL,
  `Jewelry_category_quantity` int(10) DEFAULT 0,
  `Jewelry_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jewelry`
--

INSERT INTO `jewelry` (`id`, `Jewelry_category_name`, `Jewelry_category_quantity`, `Jewelry_category_status`) VALUES
(1, 'گوشواره', 46, 0x31),
(2, 'حلقه زوج', 73, 0x31),
(3, 'گردنبند', 61, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `perfume`
--

CREATE TABLE `perfume` (
  `id` int(10) NOT NULL,
  `perfume_category_name` varchar(50) NOT NULL,
  `perfume_category_quantity` int(10) DEFAULT 0,
  `perfume_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfume`
--

INSERT INTO `perfume` (`id`, `perfume_category_name`, `perfume_category_quantity`, `perfume_category_status`) VALUES
(1, 'عطر لباس', 12, 0x31),
(2, 'دئودورانت', 60, 0x31),
(3, 'کت', 50, 0x31),
(4, 'لباس و پیراهن', 87, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(100) NOT NULL,
  `product_catag` varchar(100) NOT NULL,
  `product_title` varchar(255) NOT NULL,
  `product_price` int(100) NOT NULL,
  `product_desc` text NOT NULL,
  `product_date` varchar(50) NOT NULL,
  `product_img` text NOT NULL,
  `product_left` int(100) NOT NULL,
  `product_author` varchar(100) NOT NULL,
  `category_id` int(10) DEFAULT NULL,
  `section_id` int(10) DEFAULT NULL,
  `discounted_price` double(10,2) DEFAULT NULL,
  `image_1` varchar(50) NOT NULL,
  `image_2` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` binary(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_catag`, `product_title`, `product_price`, `product_desc`, `product_date`, `product_img`, `product_left`, `product_author`, `category_id`, `section_id`, `discounted_price`, `image_1`, `image_2`, `created_at`, `updated_at`, `status`) VALUES
(1, 'مردانه', 'کت', 75, 'کت‌های چرمی زمستانی مردانه', '', 'jacket-3.jpg', 50, 'ادمین فهاد', NULL, 7, 48.00, 'jacket-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(2, 'مردانه', 'پیراهن', 56, 'پیراهن نخی خالص', '', 'shirt-1.jpg', 50, 'ادمین فهاد', NULL, 7, 45.00, 'shirt-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(3, 'مردانه', 'کت', 65, 'کت پشمی مردانه با زیپ کامل', '', 'jacket-5.jpg', 50, 'ادمین فهاد', NULL, 7, 58.00, 'jacket-6.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(4, 'زنانه', 'دامن', 25, 'دامن میدی گل‌دار مشکی', '', 'outfits-3.jpg', 50, 'ادمین فهاد', NULL, 7, 35.00, 'outfits-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(5, 'مردانه', 'کژوال', 105, 'کفش قهوه‌ای کژوال مردانه', '', 'shoe-2.jpg', 50, 'ادمین فهاد', NULL, 7, 99.00, 'shoe-2_1.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(6, 'مردانه', 'ساعت', 170, 'ساعت جیبی با کیف چرمی', '', 'watch-3.jpg', 50, 'ادمین فهاد', NULL, 7, 150.00, 'watch-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(7, 'مردانه', 'ساعت', 120, 'ساعت هوشمند ویتال پلاس', '', 'watch-1.jpg', 50, 'ادمین فهاد', NULL, 7, 100.00, 'watch-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(8, 'زنانه', 'لباس مجلسی', 25, 'کفش مجلسی زنانه', '', 'party-wear-1.jpg', 50, 'ادمین فهاد', NULL, 7, 30.00, 'party-wear-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(9, 'مردانه', 'کت', 45, 'کت‌های چرمی زمستانی مردانه', '', 'jacket-1.jpg', 50, 'ادمین فهاد', NULL, 7, 32.00, 'jacket-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(10, 'مردانه', 'ورزشی', 64, 'کفش دویدن و کوهنوردی - مشکی', '', 'sports-2.jpg', 50, 'ادمین فهاد', NULL, 7, 58.00, 'sports-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(11, 'مردانه', 'رسمی', 65, 'کفش رسمی چرمی مردانه', '', 'shoe-1.jpg', 50, 'ادمین فهاد', NULL, 7, 50.00, 'shoe-1_1.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(12, 'زنانه', 'شلوارک', 85, 'شلوارک نخی سبک', '', 'shorts-1.jpg', 50, 'ادمین فهاد', NULL, 7, 78.00, 'shorts-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(13, 'مردانه', 'شلوار جین', 12, 'شلوار جین جدید مردانه. حراج تابستانی', '16,6,2023', 'pant2.png', 100, 'فهاد ادمین', NULL, NULL, 10.00, '', NULL, '2023-06-16 19:52:58', '2023-06-17 01:00:16', 0x31),
(14, 'مردانه', 'هودی قرمز', 15, 'هودی قرمز مردانه. طراحی جدید و رنگ روشن.', '16,6,2023', 'f3.png', 69, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-16 20:02:15', '2023-06-17 01:02:15', 0x31),
(15, 'مردانه', 'تی‌شرت مشکی', 13, 'تی‌شرت مشکی با آستین کوتاه مردانه. مناسب تابستان و زمستان. مد بالا.', '16,6,2023', 'p4.png', 99, 'فهاد ادمین', NULL, NULL, 10.00, '', NULL, '2023-06-16 20:05:07', '2023-06-17 01:05:07', 0x31),
(16, 'همه', 'تی‌شرت', 16, 'پک تی‌شرت‌های رنگی، بسته 3 تایی', '17,6,2023', 'p17.png', 150, 'فهاد ادمین', NULL, NULL, 13.00, '', NULL, '2023-06-17 05:22:01', '2023-06-17 10:22:01', 0x31),
(17, 'همه', 'عینک و لنز', 12, 'مسدودکننده نور آبی. بهبود دید در شب. ظاهری شیک.', '17,6,2023', 'g3.png', 50, 'فهاد ادمین', NULL, NULL, 10.00, '', NULL, '2023-06-17 05:24:06', '2023-06-17 10:24:06', 0x31),
(18, 'همه', 'شلوار جین', 10, 'شلوار جین برای مردان و زنان.', '17,6,2023', 'f9.png', 100, 'فهاد ادمین', NULL, NULL, 8.00, '', NULL, '2023-06-17 06:26:01', '2023-06-17 11:26:01', 0x31),
(19, 'همه', 'لباس بچه‌گانه', 15, 'لباس خرسی برای کودکان.', '17,6,2023', 'f10.png', 100, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:27:33', '2023-06-17 11:27:33', 0x31),
(20, 'همه', 'کیف', 15, 'کیف بزرگ برای حمل اکسسوری‌های مختلف.', '17,6,2023', 'topcard3.png', 50, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:28:36', '2023-06-17 11:28:36', 0x31),
(21, 'همه', 'کفش ورزشی', 15, 'کفش ورزشی. کفش دویدن. کفش بدنسازی.', '17,6,2023', 'b2.png', 20, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:30:08', '2023-06-17 11:30:08', 0x31),
(22, 'زنانه', 'کیف دستی', 15, 'کیف دستی نارنجی برای ظاهری جذاب و حمل اکسسوری.', '17,6,2023', 'p5.png', 100, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:31:26', '2023-06-17 11:31:26', 0x31),
(23, 'زنانه', 'دمپایی کژوال', 15, 'دمپایی کژوال برای زنان.', '17,6,2023', 'p7.png', 100, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:32:33', '2023-06-17 11:32:33', 0x31),
(24, 'همه', 'عطر لباس', 15, 'عطر با رایحه تازه. مناسب فضای داخلی و خارجی', '17,6,2023', 'perfume.png', 100, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:33:46', '2023-06-17 11:33:46', 0x31),
(25, 'زنانه', 'کفش رسمی', 15, 'کفش رسمی زنانه. مناسب فضای داخلی و خارجی.', '17,6,2023', 'p10.jpg', 100, 'فهاد ادمین', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:35:09', '2023-06-17 11:35:09', 0x31),
(26, 'زنانه', 'جوراب', 10, 'جوراب برای فضای داخلی و خارجی.', '17,6,2023', 'f2.png', 100, 'فهاد ادمین', NULL, NULL, 5.00, '', NULL, '2023-06-17 06:38:25', '2023-06-17 11:38:25', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `review` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `review`) VALUES
(1, 'اقصو فهد', 'لورم ایپسوم دولور سیت آمت، کنسکتتور ادیپیسینگ الیت. کیدم اد فیگیت، ایتاکه دولوره کولپا ایپسا فوگا، ایلوم، ماکسیمه اکسرجیتاتیونم کمودی نیهیل نوبس نولا سیملیک کویبوسدام سد اکسپدیتا پروویدنت'),
(2, 'ایفاد', 'لورم ایپسوم دولور سیت آمت، کنسکتتور ادیپیسینگ الیت. کیدم اد فیگیت، ایتاکه دولوره کولپا ایپسا فوگا، ایلوم، ماکسیمه اکسرجیتاتیونم کمودی نیهیل نوبس نولا سیملیک کویبوسدام سد اکسپدیتا پروویدنت'),
(3, 'اوا سیلک', 'لورم ایپسوم دولور سیت آمت، کنسکتتور ادیپیسینگ الیت. کیدم اد فیگیت، ایتاکه دولوره کولپا ایپسا فوگا، ایلوم، ماکسیمه اکسرجیتاتیونم کمودی نیهیل نوبس نولا سیملیک کویبوسدام سد اکسپدیتا پروویدنت');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` binary(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `name`, `status`) VALUES
(2, 'ورود جدید', 0x31),
(3, 'پرطرفدار', 0x31),
(4, 'برترین‌ها', 0x31),
(5, 'پیشنهاد روز', 0x31),
(6, 'پرفروش', 0x31),
(7, 'محصولات جدید', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `website_name` varchar(60) NOT NULL,
  `website_logo` varchar(50) NOT NULL,
  `website_footer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`website_name`, `website_logo`, `website_footer`) VALUES
('اچ‌سی‌ای تجارت الکترونیک', 'HCA-E-COMMERCE.png', 'اچ‌سی‌ای تجارت الکترونیک');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bags`
--
ALTER TABLE `bags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_bar`
--
ALTER TABLE `category_bar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_electronics`
--
ALTER TABLE `category_electronics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outfits`
--
ALTER TABLE `outfits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cosmetics`
--
ALTER TABLE `cosmetics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `deal_of_the_day`
--
ALTER TABLE `deal_of_the_day`
  ADD PRIMARY KEY (`deal_id`);

--
-- Indexes for table `footwear`
--
ALTER TABLE `footwear`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `glasses`
--
ALTER TABLE `glasses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jewelry`
--
ALTER TABLE `jewelry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perfume`
--
ALTER TABLE `perfume`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `category_bar`
--
ALTER TABLE `category_bar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;