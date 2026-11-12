-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 04:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web`
--

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
(1, '', '', 20, 'banner-1.jpg', 0x31),
(2, '', '', 15, 'banner-2.jpg', 0x31),
(3, '', '', 29, 'banner-3.jpg', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `bundles`
--

CREATE TABLE `bundles` (
  `id` int(10) NOT NULL,
  `bundles_category_name` varchar(50) NOT NULL,
  `bundles_category_quantity` int(10) DEFAULT 0,
  `bundles_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bundles`
--

INSERT INTO `bundles` (`id`, `bundles_category_name`, `bundles_category_quantity`, `bundles_category_status`) VALUES
(1, 'کلش اف کلنز', 2, 0x01),
(2, 'کالاف دیوتی موبایل', 1, 0x01),
(3, 'پابجی موبایل', 2, 0x01);

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
(1, 'کلش اف کلنز', 'clash.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31),
(2, 'کالاف دیوتی موبایل', 'codm.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31),
(3, 'پابجی موبایل', 'pubgm.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31),
(4, 'فری فایر', 'freefire.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31),
(5, 'موبایل لجند', 'mobilelegends.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31),
(6, 'براول استارز', 'brawlstars.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31),
(7, 'وارزون موبایل', 'warzone.svg', '2025-04-24 16:22:43', '2025-04-24 19:52:43', 0x31);

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
(1, 'کلش اف کلنز', 53, 'clash.svg', 0x31),
(2, 'کالاف دیوتی موبایل', 68, 'codm.svg', 0x31),
(3, 'پابچی موبایل', 84, 'pubgm.svg', 0x31),
(4, 'فری فایر', 35, 'freefire.svg', 0x31),
(5, 'موبایل لجند', 16, 'mobilelegends.svg', 0x31),
(6, 'براول استارز', 27, 'brawlstars.svg', 0x31),
(7, 'وارزون موبایل', 39, 'warzone.svg', 0x31);

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
(1, 'فری فایر', 1),
(2, 'موبایل لجند', 1),
(3, ' براول استارز', 1),
(4, ' وارزون موبایل', 1),
(5, 'کلش اف کلنز', 1);

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
(1, 'adminstrator', 'admin@gmail.com', 'admin', '03469589557', 'ایران,تهران', 'admin'),
(28, 'حسین کاظمی نیا', 'hossein.kazemi965@gmail.com', '29a5091f60', '09191234567', 'مشهد، خیابان امام رضا', 'normal'),
(29, 'مهدی احمدی', 'mahdi.ahmadi318@yahoo.com', '95088cd4c6', '09361234567', 'تبریز، خیابان آزادی', 'normal'),
(30, 'سجاد رحیمی', 'sajjad.rahimi371@iran.ir', '9c6c361668', '09181234567', 'کرج، مهرشهر', 'normal'),
(31, 'امیر جعفری', 'amir.jafari178@gmail.com', '6bf0e264e2', '09221234567', 'قم، بلوار امین', 'normal'),
(32, 'نیما یوسفی', 'nima.yousefi52@yahoo.com', '3e520b9f68', '09371234567', 'اهواز، کیانپارس', 'normal'),
(33, 'کیانوش شریفی', 'kianoush.sharifi395@iran.ir', '6dec9e3491', '09101234567', 'رشت، گلسار', 'normal'),
(34, 'پارسا کریمی', 'parsa.karimi44@gmail.com', '252974bea6', '09231234567', 'یزد، صفائیه', 'normal'),
(35, 'فاطمه اکبری', 'fateme.akbari284@yahoo.com', 'db8be254d0', '09381234567', 'تهران، سعادت‌آباد', 'normal'),
(36, 'زهرا علوی', 'zahra.alavi999@iran.ir', '0b53124734', '09111234567', 'اصفهان، خیابان نظر', 'normal'),
(37, 'مریم نجفی', 'maryam.najafi433@gmail.com', '8ec05d018e', '09241234567', 'شیراز، معالی‌آباد', 'normal'),
(38, 'نازنین رامزانی', 'nazanin.ramezani241@yahoo.com', 'c4b6635841', '09151234567', 'مشهد، بلوار سجاد', 'normal'),
(39, 'سارا امینی', 'sara.amini689@iran.ir', 'c51aaffc84', '09391234567', 'تبریز، ولیعصر', 'normal'),
(40, 'الناز قاسمی', 'elnaz.ghasemi917@gmail.com', '9a29867db6', '09161234567', 'کرج، عظیمیه', 'normal'),
(41, 'مهسا رستمی', 'mahsa.rostami222@yahoo.com', '1b8c7b8200', '09251234567', 'قم، خیابان انقلاب', 'normal'),
(42, 'پریسا حسنی', 'parisa.hasani965@iran.ir', '00804d1c5e', '09301234567', 'اهواز، زیتون', 'normal'),
(43, 'هانیه شاکری', 'hanie.shakeri350@gmail.com', '7e48227169', '09171234567', 'رشت، بلوار معلم', 'normal'),
(44, 'آتنا مرادی', 'atena.moradi977@yahoo.com', 'b130a1f8e2', '09261234567', 'یزد، بلوار جمهوری', 'normal'),
(45, 'بهرام شفیعی', 'bahram.shafiei404@iran.ir', '7bf9b246cc', '09131234567', 'تهران، نیاوران', 'normal'),
(46, 'کاوه نوری', 'kaveh.noori898@gmail.com', '15518335cb', '09321234567', 'اصفهان، خیابان کاوه', 'normal'),
(47, 'بهزاد طاهری', 'behzad.taheri428@yahoo.com', '04dc5c43f7', '09271234567', 'شیراز، بلوار فرهنگ', 'normal'),
(48, 'اشکان بیات', 'ashkan.bayat63@iran.ir', '965b1d8947', '09141234567', 'مشهد، خیابان احمدآباد', 'normal'),
(49, 'شایان امیری', 'shayan.amiri343@gmail.com', 'ac372dbc83', '09331234567', 'تبریز، خیابان شریعتی', 'normal'),
(50, 'آرمان بهشتی', 'arman.beheshti956@yahoo.com', '99d560d164', '09281234567', 'کرج، جهانشهر', 'normal'),
(51, 'کیوان شجاعی', 'keyvan.shojaei83@iran.ir', '00174841c8', '09121234567', 'قم، بلوار غدیر', 'normal'),
(52, 'رامین کسرایی', 'ramin.kasraei967@gmail.com', '86635b9362', '09311234567', 'اهواز، بلوار پاسداران', 'normal'),
(53, 'هومن شریعت', 'hooman.shariat643@yahoo.com', 'e2ffd683d8', '09201234567', 'رشت، خیابان مطهری', 'normal'),
(54, 'فرهاد قائمی', 'farhad.ghaemi522@iran.ir', '2146d99d60', '09181234567', 'یزد، خیابان کاشانی', 'normal'),
(55, 'مینا حیدری', 'mina.heidari137@gmail.com', 'b31ebb2b20', '09371234567', 'تهران، جردن', 'normal'),
(56, 'نسترن فرهمند', 'nastaran.farahmand904@yahoo.com', '7780e393ea', '09231234567', 'اصفهان، خیابان بزرگمهر', 'normal'),
(57, 'رویا معینی', 'roya.moeini943@iran.ir', '177f475f8b', '09101234567', 'شیراز، خیابان زند', 'normal'),
(58, 'شیدا رئوفی', 'shida.raoufi786@gmail.com', '50f2db2d46', '09361234567', 'مشهد، بلوار وکیل‌آباد', 'normal'),
(59, 'ترانه بهرامی', 'taraneh.bahrani449@yahoo.com', '71d8811384', '09221234567', 'تبریز، خیابان امام', 'normal'),
(60, 'لیلا شاداب', 'leila.shadab472@iran.ir', '0c65e9ce76', '09191234567', 'کرج، بلوار موذن', 'normal'),
(61, 'مهناز افشار', 'mahnaz.afshar935@gmail.com', '011c3bae6b', '09351234567', 'قم، خیابان صفاشهر', 'normal'),
(62, 'شبنم قلی‌زاده', 'shabnam.gholizadeh510@yahoo.com', 'fc65af4156', '09211234567', 'اهواز، خیابان نادری', 'normal'),
(63, 'پریناز ایزدیار', 'parinaz.izadyar278@iran.ir', '2538dd094d', '09151234567', 'رشت، بلوار انزلی', 'normal'),
(64, 'بهناز جعفری', 'behnaz.jafari708@gmail.com', '7dd67d49eb', '09341234567', 'یزد، خیابان مهدی', 'normal'),
(65, 'آرش کمانگیر', 'arash.kamangir944@yahoo.com', '93c97180ab', '09291234567', 'تهران، خیابان شریعتی', 'normal'),
(66, 'داریوش فروهر', 'dariush.forouhar580@iran.ir', 'bb7ff80b98', '09131234567', 'اصفهان، خیابان حافظ', 'normal'),
(67, 'فریدون مشیری', 'fereydoun.moshiri530@gmail.com', '070db1d24f', '09321234567', 'شیراز، بلوار ارم', 'normal'),
(68, 'سهراب سپهری', 'sohrab.sepehri850@yahoo.com', '61ca809677', '09271234567', 'مشهد، خیابان کوهسنگی', 'normal'),
(69, 'سیمین دانشور', 'simin.daneshvar379@iran.ir', '575319cd38', '09141234567', 'تبریز، خیابان ارتش', 'normal'),
(70, 'پروین اعتصامی', 'parvin.etesami558@gmail.com', '97fd23ba0c', '09331234567', 'کرج، خیابان چمران', 'normal'),
(71, 'فروغ فرخزاد', 'forough.farrokhzad198@yahoo.com', '4f8b2d1658', '09281234567', 'قم، خیابان ارم', 'normal'),
(72, 'نیما یوشیج', 'nima.yushij736@iran.ir', 'b2d319692b', '09121234567', 'اهواز، خیابان شریعتی', 'normal'),
(73, 'احمد شاملو', 'ahmad.shamlou204@gmail.com', 'fb2e444dde', '09311234567', 'رشت، خیابان لاکانی', 'normal'),
(74, 'مهدی اخوان', 'mehdi.akhvan262@yahoo.com', '7bc47551d3', '09201234567', 'یزد، خیابان فرخی', 'normal'),
(75, 'هوشنگ ابتهاج', 'houshang.ebtehaj139@iran.ir', '477e256818', '09181234567', 'تهران، خیابان آفریقا', 'normal'),
(76, 'خسرو شکیبایی', 'khosrow.shakibai300@gmail.com', '4577468572', '09371234567', 'اصفهان، خیابان شیخ صدوق', 'normal'),
(77, 'بهروز وثوقی', 'behrouz.vosoughi442@yahoo.com', '1368bed4b8', '09231234567', 'شیراز، خیابان قصرالدشت', 'normal'),
(78, 'جمشید هاشم‌پور', 'jamshid.hashempour704@iran.ir', '99ca249e90', '09101234567', 'مشهد، خیابان فلسطین', 'normal'),
(79, 'محمدرضا شریفی‌نیا', 'mohammadreza.sharifinia402@gmail.com', '2dbe6bab8d', '09361234567', 'تبریز، خیابان دانشگاه', 'normal'),
(80, 'اکبر عبدی', 'akbar.abdi306@yahoo.com', 'cdf78c62b4', '09221234567', 'کرج، خیابان بهشتی', 'normal'),
(81, 'داریوش ارجمند', 'dariush.arjomand194@iran.ir', 'b45caf9a3a', '09191234567', 'قم، خیابان بهار', 'normal'),
(82, 'پرویز پرستویی', 'parviz.parastui944@gmail.com', '45927aaea5', '09351234567', 'اهواز، خیابان طالقانی', 'normal'),
(83, 'رضا کیانیان', 'reza.kianian189@yahoo.com', '80e9500062', '09211234567', 'رشت، خیابان سبزه‌میدان', 'normal'),
(84, 'علی نصیریان', 'ali.nasirian100@iran.ir', 'd3e550b5d0', '09151234567', 'یزد، خیابان قیام', 'normal');

-- --------------------------------------------------------

--
-- Table structure for table `deal_of_the_day`
--

CREATE TABLE `deal_of_the_day` (
  `deal_id` int(10) NOT NULL,
  `deal_title` text NOT NULL,
  `deal_description` text NOT NULL,
  `deal_net_price` double(10,0) NOT NULL,
  `deal_discounted_price` double(10,0) NOT NULL,
  `available_deal` int(10) NOT NULL,
  `sold_deal` int(10) NOT NULL,
  `deal_image` varchar(50) NOT NULL,
  `deal_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deal_of_the_day`
--

INSERT INTO `deal_of_the_day` (`deal_id`, `deal_title`, `deal_description`, `deal_net_price`, `deal_discounted_price`, `available_deal`, `sold_deal`, `deal_image`, `deal_status`) VALUES
(1, '۷۴۰ سی پی کالاف دیوتی موبایل', 'اگر درخرید سیپی کالاف دیوتی  در بازی مورد علاقه خود با مشکل مواجه هستید و نمی توانید در برابر گرفتن پوسته جدید...', 723000, 750000, 36, 121, 'cp740.webp', 0x31),
(2, 'بسته ۶۶۰ یوسی', 'برای دریافت آسان و سریع و خرید یوسی پابجی موبایل می توانید از بسته های نیکوجم استفاده کنید . برای خرید یوسی فقط...', 799000, 950, 59, 134, 'uc660.webp', 0x31);

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
(1, 'کلش اف کلنز', 61, 0x31),
(2, 'کالاف دیوتی موبایل', 60, 0x31),
(4, 'پابچی موبایل', 50, 0x31),
(5, 'فری فایر', 87, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(100) NOT NULL,
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

INSERT INTO `products` (`product_id`, `product_title`, `product_price`, `product_desc`, `product_date`, `product_img`, `product_left`, `product_author`, `category_id`, `section_id`, `discounted_price`, `image_1`, `image_2`, `created_at`, `updated_at`, `status`) VALUES
(29, '۸۰ سی پی کالاف دیوتی موبایل', 95000, '0', '23,4,2025', 'cp80.png', 35, 'adminstrator', 2, NULL, 75000.00, '', NULL, '2025-04-23 16:03:01', '2025-04-24 19:57:03', 0x31),
(31, '۱۶۰ سی پی کالاف دیوتی موبایل', 200000, '0', '23,4,2025', 'cp160.png', 29, 'adminstrator', 2, NULL, 150000.00, '', NULL, '2025-04-23 16:13:03', '2025-04-24 19:57:03', 0x31),
(32, '۳۲۰ سی پی کالاف دیوتی موبایل', 399000, '0', '23,4,2025', 'cp320.png', 60, 'adminstrator', 2, NULL, 300000.00, '', NULL, '2025-04-23 16:29:35', '2025-04-24 19:57:03', 0x31),
(33, '۷۴۰ سی پی کالاف دیوتی موبایل', 750000, '0', '23,4,2025', 'cp740.png', 3, 'adminstrator', 2, NULL, 698900.00, '', NULL, '2025-04-23 16:32:43', '2025-04-24 19:57:03', 0x31),
(34, 'بلیط طلایی کلش اف کلنز از استور ۷ دلاری', 550000, '0', '23,4,2025', 'clash7do.png', 23, 'adminstrator', 1, NULL, 419900.00, '', NULL, '2025-04-23 16:40:57', '2025-04-24 19:57:03', 0x31),
(35, 'بلیط رویداد کشتی کج کلش اف کلنز', 444900, '0', '23,4,2025', 'clashkosh.png', 7, 'adminstrator', 1, NULL, 299900.00, '', NULL, '2025-04-23 16:44:16', '2025-04-24 19:57:03', 0x31),
(36, 'بسته ۶۰ یوسی پابجی', 99000, '0', '23,4,2025', 'uc60.png', 1, 'adminstrator', 3, NULL, 74900.00, '', NULL, '2025-04-23 16:50:36', '2025-04-24 19:57:03', 0x31),
(37, 'بسته ۱۲۰ یوسی پابجی', 199000, '0', '23,4,2025', 'uc120.png', 48, 'adminstrator', 3, NULL, 148900.00, '', NULL, '2025-04-23 16:55:08', '2025-04-24 19:57:03', 0x31),
(38, 'بسته ۶۶۰ یوسی پابجی', 950000, '0', '23,4,2025', 'uc660.png', 22, 'adminstrator', 3, NULL, 746900.00, '', NULL, '2025-04-23 16:57:49', '2025-04-24 19:57:03', 0x31),
(39, 'بسته ۱۱۹۵۰ یوسی پابجی', 179000, 'برای دریافت آسان و سریع و خرید یوسی پابجی موبایل می توانید از بسته های نیکوجم استفاده کنید . برای خرید یوسی فقط کافیست آیدی و اسم بازی خود را وارد کنید و در سریعترین زمان یوسی خود را از نیکوجم دریافت کنید و از بازی کردن لذت ببرید. یوسی یک نوع واحد پولی یا همان ارزی در بازی پابجی موبایل می باشد ، همانند خرید سی پی کالاف دیوتی موبایل در بازی کالاف و یا خرید جم فری فایر. شما با استفاده از یوسی یا همان دیاموند می توانید در بازی اسلحه ، حیوانات خانگی ، اسکین و یا ماشین خاصی را خریداری کنید. همچنین می توانید در گردونه شانس نیکوجم شرکت کنید و یوسی رایگان دریافت کنید.', '23,4,2025', 'uc11950.png', 4, 'adminstrator', 3, NULL, 111500.00, '', NULL, '2025-04-23 17:08:06', '2025-04-25 00:18:12', 0x31),
(40, 'بسته ۲۱۰ جم فری فایر', 230000, '0', '23,4,2025', 'gem210.png', 7, 'adminstrator', 4, NULL, 157900.00, '', NULL, '2025-04-23 17:39:57', '2025-04-24 19:57:03', 0x31),
(41, 'بسته ۱۰۰ جم فری فایر', 110000, '0', '23,4,2025', 'gem100.png', 49, 'adminstrator', 4, NULL, 67900.00, '', NULL, '2025-04-23 17:45:44', '2025-04-24 19:57:03', 0x31),
(42, 'بسته ۶۸۲ جم فری فایر ', 550000, '0', '23,4,2025', 'gem682.png', 4, 'adminstrator', 4, NULL, 490000.00, '', NULL, '2025-04-23 18:03:35', '2025-04-24 19:57:03', 0x31),
(50, '۸۰ سی پی کالاف دیوتی موبایل', 150000, 'پکیج اقتصادی برای خرید آیتم‌ها', '24,4,2025', 'cp80.png', 100, 'adminstrator', 2, NULL, 120000.00, '', NULL, '2025-04-24 17:45:50', '2025-04-24 21:15:50', 0x31),
(51, '۵۰۰ جم کلش اف کلنز', 300000, 'منابع برای ارتقای دهکده', '24,4,2025', 'gem500.png', 50, 'adminstrator', 1, NULL, 240000.00, '', NULL, '2025-04-24 17:45:50', '2025-04-24 21:15:50', 0x31),
(52, '۱۳۲۰ یوسی پابجی موبایل', 500000, 'برای خرید اسکین و آیتم‌های ویژه', '24,4,2025', 'uc1320.png', 80, 'adminstrator', 3, NULL, 400000.00, '', NULL, '2025-04-24 17:45:50', '2025-04-24 21:22:29', 0x31),
(53, '۱۰۰ جم فری فایر', 600000, 'الماس برای خرید اسکین و کاراکتر', '24,4,2025', 'gem100.png', 60, 'adminstrator', 4, NULL, 480000.00, '', NULL, '2025-04-24 17:45:50', '2025-04-24 21:24:39', 0x31),
(54, '۲۲۳ جم موبایل لجند', 250000, 'برای خرید هیروها', '24,4,2025', 'point223.png', 70, 'adminstrator', 5, NULL, 200000.00, '', NULL, '2025-04-24 17:45:50', '2025-04-24 21:26:40', 0x31),
(55, '۱۳۴ جم موبایل لجند', 240000, '0', '25,4,2025', 'gem134.png', 15, 'adminstrator', 5, NULL, 209900.00, '', NULL, '2025-04-25 08:27:09', '2025-04-25 11:57:09', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `review` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `review`) VALUES
(1, 'علی رضایی', 'واقعا عالی بود! سی‌پی کالاف رو تو کمتر از 10 دقیقه برام واریز کردن. پشتیبانی هم خیلی سریع جواب داد.'),
(2, 'محمد حسینی', 'یو‌سی پابجی رو با قیمت خوب خریدم. فقط یه کم طول کشید تا واریز بشه، ولی در کل راضیم.'),
(3, 'رضا محمدی', 'سایتتون حرف نداره! جم فری‌فایر رو با تخفیف گرفتم و فوری به اکانتم اضافه شد.'),
(4, 'حسین کاظمی', 'اولین بار بود از اینجا سی‌پی خریدم. خیلی سریع و قابل اعتماد بود. حتما بازم میام!'),
(5, 'مهدی احمدی', 'پشتیبانی شما عالیه! یه مشکل کوچیک تو واریز یو‌سی داشتم که سریع حلش کردن.'),
(6, 'سجاد رحیمی', 'قیمت‌ها نسبت به جاهای دیگه خیلی بهتره. سی‌پی کالاف رو با خیال راحت خریدم.'),
(7, 'امیر جعفری', 'فوق‌العاده! یو‌سی پابجی تو 5 دقیقه واریز شد. حتما به دوستام معرفیتون می‌کنم.'),
(8, 'نیما یوسفی', 'جم کلش آف کلنز گرفتم، سرعت واریز خوب بود ولی ای کاش تخفیف بیشتری داشت.'),
(9, 'کیانوش شریفی', 'سایت مطمئنیه، چند بار سی‌پی و یو‌سی خریدم و همیشه راضی بودم.'),
(10, 'پارسا کریمی', 'واقعا سرعت تحویل سی‌پی کالاف عالی بود. پشتیبانی هم خیلی مودب و حرفه‌ای.'),
(11, 'فاطمه اکبری', 'یو‌سی پابجی رو سریع برام واریز کردن. فقط یه بار پیامم رو دیر جواب دادن.'),
(12, 'زهرا علوی', 'اولین تجربه‌ام از خرید جم فری‌فایر بود. خیلی راحت و سریع بود، مرسی!'),
(13, 'مریم نجفی', 'سی‌پی کالاف رو با تخفیف خوب گرفتم. فقط امیدوارم همیشه این قیمتا بمونه.'),
(14, 'نازنین رامزانی', 'عالی! یو‌سی پابجی تو کمتر از 15 دقیقه به اکانتم اضافه شد. خیلی راضیم.'),
(15, 'سارا امینی', 'سایتتون خیلی خوبه، ولی ای کاش امکان پرداخت با ارز دیجیتال هم اضافه کنید.'),
(16, 'الناز قاسمی', 'جم کلش رو با قیمت مناسب خریدم. تحویلش فوری بود و مشکلی نداشتم.'),
(17, 'مهسا رستمی', 'پشتیبانی شما حرف نداره! یه مشکل تو واریز سی‌پی داشتم که سریع حل شد.'),
(18, 'پریسا حسنی', 'یو‌سی پابجی رو با تخفیف گرفتم. سرعت تحویل هم عالی بود. مرسی از تیم خوبتون.'),
(19, 'هانیه شاکری', 'چند بار از اینجا سی‌پی خریدم، همیشه سریع و مطمئن بوده. توصیه می‌کنم!'),
(20, 'آتنا مرادی', 'جم فری‌فایر رو تو 10 دقیقه برام واریز کردن. خیلی راضیم، بازم می‌خرم.'),
(21, 'بهرام شفیعی', 'سایت قابل اعتمادیه، ولی یه بار واریز یو‌سی یه کم طول کشید. در کل خوبه.'),
(22, 'کاوه نوری', 'سی‌پی کالاف رو با قیمت عالی گرفتم. پشتیبانی هم خیلی سریع جواب داد.'),
(23, 'بهزاد طاهری', 'واقعا راضیم! یو‌سی پابجی فوری واریز شد و هیچ مشکلی نبود.'),
(24, 'اشکان بیات', 'جم کلش آف کلنز رو سریع تحویل دادن. فقط ای کاش تخفیفات بیشتری بذارید.');

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
('آسانپِی', '22.png', 'آسانپِی');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `bundles`
--
ALTER TABLE `bundles`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `outfits`
--
ALTER TABLE `outfits`
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
  MODIFY `customer_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
