-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2015 at 03:19 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ciblog`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_key` varchar(32) NOT NULL,
  `cat_name` varchar(64) NOT NULL,
  `cat_display_info` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_key`, `cat_name`, `cat_display_info`) VALUES
(1, 'blog', 'blog', 1),
(2, 'standalone', 'standalone', 0);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_admin_id` int(11) DEFAULT NULL,
  `post_title` varchar(128) NOT NULL DEFAULT '""',
  `post_body` text NOT NULL,
  `post_slug` varchar(64) NOT NULL,
  `post_created` timestamp NULL DEFAULT NULL,
  `post_updated` timestamp NULL DEFAULT NULL,
  `post_category` int(10) unsigned NOT NULL,
  `post_draft` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `post_admin_id`, `post_title`, `post_body`, `post_slug`, `post_created`, `post_updated`, `post_category`, `post_draft`) VALUES
(22, 1, '¿Que podemos hacer por vos?', '<p>Nos especializamos en consultor&iacute;a de software para empresas y agencias de publicidad.</p>\r\n\r\n<p>Podemos brindarte servicios de desarrollo de software en tecnolog&iacute;as c&oacute;mo:</p>\r\n\r\n<ul>\r\n	<li>Sistemas Web Html y/o Flash.</li>\r\n	<li>Aplicaciones M&oacute;viles para iPhone/iPad y dispositivos Android.</li>\r\n	<li>Juegos para PC nativos (Win/Linux/OSX) y Web (Flash).</li>\r\n	<li>Aplicaciones de Escritorio.</li>\r\n	<li>Backend y Base de Datos de Servidores.</li>\r\n</ul>\r\n\r\n<p>Brindamos asesoramiento acerca de las tecnolog&iacute;as m&aacute;s apropiadas para utilizar en tu proyecto.</p>\r\n\r\n<p>Llevamos a cabo el desarrollo y la producci&oacute;n, con atenci&oacute;n al detalles, desde el primer d&iacute;a.</p>\r\n\r\n<p>La excelencia y la entrega a tiempo son nuestras metas principales, como nuestros clientes ya establecidos conocen firmemente.</p>\r\n\r\n<p><a href="/contacto" target="_blank">Contactanos</a> para comenzar a trabajar juntos.</p>\r\n\r\n<p>Cordialmente, <em>el equipo de <strong>Ensoft.</strong></em></p>\r\n', 'que-podemos-hacer-por-vos', '2012-03-31 22:30:52', '2012-03-31 22:30:52', 2, 0),
(27, 1, 'Especificaciones de Entropia Engine++', '<p>La tecnolog&iacute;a de desarrollo propio que utilizaremos es <a href="http://www.eepp.com.ar" target="_blank">Entropia Engine++</a>, es un motor para el desarrollo de videojuegos 2D multiplataforma, creado para facilitar la creaci&oacute;n y distribuci&oacute;n de los mismos, pensado para un rendimiento &oacute;ptimo en cualquier plataforma en la que sea utilizado.</p>\r\n\r\n<h3>Funcionalidad multiplataforma:</h3>\r\n\r\n<ul>\r\n	<li>Soporta oficialmente Linux, Windows, Mac OS X.</li>\r\n	<li>Ha sido probado tambi&eacute;n en FreeBSD, Solaris and Haiku OS.</li>\r\n	<li>Ha sido portado a Android, se encuentra en etapa de desarrollo.</li>\r\n	<li>El port a iOS est&aacute; en desarrollo.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo Gr&aacute;fico:</h3>\r\n\r\n<ul>\r\n	<li>Soporte para OpenGL 2 (fixed-pipeline), OpenGL 3 (programmable-pipeline), OpenGL ES 2 and OpenGL ES 1.</li>\r\n	<li>Batch Renderer (todo el rendering es apilado en lotes, para un &oacute;ptimo rendimiento).</li>\r\n	<li>Fuentes TrueType y fuentes de texturas (cargadas de texture atlas).</li>\r\n	<li>Soporte de Frame Buffers (FBO y PBuffer).</li>\r\n	<li>Soporte de Shaders.</li>\r\n	<li>Soporte de Vertex Buffer Object.</li>\r\n	<li>Sistema de Part&iacute;culas.</li>\r\n	<li>Consola gr&aacute;fica.</li>\r\n	<li>Sprites animados.</li>\r\n	<li>Soporte a Texture Atlas (incluye aplicaci&oacute;n para crear y manipular los mismos).</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Ventana:</h3>\r\n\r\n<ul>\r\n	<li>M&oacute;dulo basado en backends, esto significa que puedes cambiar de sistemas especializados para controlar el manejo de la ventana, los controles y el contexto gr&aacute;fico de forma transparente.</li>\r\n	<li>Actualmente soporta como backends SDL 1.2, SDL 1.3 y Allegro 5.</li>\r\n	<li>Soporte de clipboard.</li>\r\n	<li>Cursores color por hardware.</li>\r\n	<li>Soporte de Joystick.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Audio:</h3>\r\n\r\n<ul>\r\n	<li>Motor de audio con OpenAL, con extensible soporte de formatos de audio, soporta nativamente OGG y todos los formatos soportados por la librer&iacute;a libsndfile.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de F&iacute;sica:</h3>\r\n\r\n<ul>\r\n	<li>Envoltura orientada a objetos del motor de f&iacute;sicas Chipmunk Physics.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Interfaz Gr&aacute;fica:</h3>\r\n\r\n<ul>\r\n	<li>Sistema de interfaz gr&aacute;fica con todas las funciones necesarias para un completo desarrollo, con soporte de pieles, animaciones, escalado, rotaciones, hardware clipping, eventos, mensajes, etc. Todos los controles b&aacute;sicos implementados (botones, textbox, combobox, inputbox, menues, listbox, etc).</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Sistema:</h3>\r\n\r\n<ul>\r\n	<li>Provee de todas las cosas b&aacute;sicas para el soporte multi-threading del motor, soporte de formatos de archivo empaquetados, y m&aacute;s.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo Base:</h3>\r\n\r\n<ul>\r\n	<li>Manejador de memoria personalizable . Usado por defecto en modo debug para localizar memory leaks. Soporte para UTF8, UTF-16, UTF-32, Ansi, Wide Char.</li>\r\n	<li>Clase de String utilizando UTF-32 internamente.</li>\r\n	<li>Macros para Debug</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Utilidades:</h3>\r\n\r\n<ul>\r\n	<li>Funciones de prop&oacute;sito general y templates (vectores, pol&oacute;gonos, colores, etc) de ayuda para el motor y las aplicaciones.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Matem&aacute;tica:</h3>\r\n\r\n<ul>\r\n	<li>Algunas funciones matem&aacute;ticas para facilitar el desarrollo, Incluye una implementaci&oacute;n de n&uacute;meros pseudo-aleatorios, Mersenne Twister.</li>\r\n</ul>\r\n\r\n<h3>M&oacute;dulo de Juego:</h3>\r\n\r\n<ul>\r\n	<li>Mapas 2D (tiled).</li>\r\n	<li>Editor de mapas.</li>\r\n	<li>Soporte para carga de recursos multi-threaded (texturas, sonidos, fuentes, etc).</li>\r\n</ul>\r\n', 'especificaciones-de-entropia-engine', '2012-04-06 04:36:22', '2012-04-06 04:36:22', 2, 0),
(28, 1, 'Entropia Engine++', '<h2>Caracter&iacute;sticas</h2>\r\n\r\n<p><a href="http://bitbucket.org/SpartanJ/eepp-dev" target="_blank">Entropia Engine++</a>, es un motor para el desarrollo de videojuegos 2D multiplataforma, creado para facilitar la creaci&oacute;n y distribuci&oacute;n de los mismos, pensado para un rendimiento &oacute;ptimo en cualquier plataforma en la que sea utilizado.</p>\r\n\r\n<h2>Portabilidad</h2>\r\n\r\n<p>El c&oacute;digo del mismo puede ser compilado para una cantidad sustancial de plataformas, de diferente naturaleza sin modificar el c&oacute;digo. Por ejemplo el mismo juego, puede ser compilado para Windows, Linux, Mac OS, iOS (iPhone y iPad) y Android (Tablets y SmartPhones).</p>\r\n\r\n<h2>Diferencias</h2>\r\n\r\n<p>Se diferencia principalmente de otros motores de desarrollo de videojuegos 2D comerciales y c&oacute;digo abierto es en la facilidad de portabilidad del mismo gracias a su modularidad y extensibilidad, provee integramente m&aacute;s m&oacute;dulos de los que ofrecen otras alternativas, entre ellos el m&oacute;dulo de interfaz gr&aacute;fica, que reduce los tiempos de desarrollo en gran medida. Por otro lado, nuestra elecci&oacute;n fue trabajar con un lenguaje compilado y nativo para las plataformas, para poder aprovechar al m&aacute;ximo el hardware de cada una de estas, pudiendo tener control total del manejo de la memoria, y teniendo un rendimiento &oacute;ptimo en todas las plataformas.</p>\r\n\r\n<h2>Desarrollo</h2>\r\n\r\n<p>El motor se encuentra en activo desarrollo y mantenido por uno de los miembros del equipo, se planifica expandir el mismo de acuerdo a las necesidades que surjan para el desarrollo del videojuego ( entre ellas, completar el portado del mismo a las plataformas m&oacute;biles, m&oacute;dulo de networking, m&oacute;dulo de scripting ).</p>\r\n\r\n<h2>Licencia</h2>\r\n\r\n<p>El motor es de c&oacute;digo abierto, bajo la licencia MIT<a href="#fn:1">1</a>, lo que permite utilizarlo libremente tanto para aplicaciones open source como para aplicaciones comerciales.</p>\r\n\r\n<h2>C&oacute;digo de ejemplo en C++:</h2>\r\n\r\n<pre>\r\n<code class="language-cpp">#include &lt;eepp.hpp&gt; \r\n\r\nEE::Window::Window * win = NULL;\r\n\r\nvoid MainLoop()\r\n{\r\n	// Clear the screen buffer\r\n	win-&gt;Clear();\r\n\r\n	// Create an instance of the primitive renderer\r\n	Primitives p;\r\n\r\n	// Change the color\r\n	p.SetColor( ColorA( 0, 255, 0, 150 ) );\r\n\r\n	// Update the input\r\n	win-&gt;GetInput()-&gt;Update();\r\n\r\n	// Check if ESCAPE key is pressed\r\n	if ( win-&gt;GetInput()-&gt;IsKeyDown( KEY_ESCAPE ) ) {\r\n		// Close the window\r\n		win-&gt;Close();\r\n	}\r\n\r\n	// Draw a circle\r\n	p.DrawCircle( Vector2f( win-&gt;GetWidth() * 0.5f, win-&gt;GetHeight() * 0.5f ), 200, 50 );\r\n\r\n	// Draw frame\r\n	win-&gt;Display();\r\n}\r\n\r\n// EE_MAIN_FUNC is needed by some platforms to be able to find the real application main\r\nEE_MAIN_FUNC int main (int argc, char * argv [])\r\n{\r\n	// Create a new window with vsync enabled\r\n	win = Engine::instance()-&gt;CreateWindow( WindowSettings( 960, 640, "eepp - Empty Window" ), ContextSettings( true ) );\r\n\r\n	// Check if created\r\n	if ( win-&gt;Created() ) {\r\n		// Set window background color\r\n		win-&gt;BackColor( RGB( 50, 50, 50 ) );\r\n\r\n		// Set the MainLoop function and run it\r\n		// This is the application loop, it will loop until the window is closed.\r\n		// This is only a requirement if you want to support Emscripten builds ( WebGL + Canvas ).\r\n		// This is the same as, except for Emscripten.\r\n		// while ( win-&gt;Running() )\r\n		// {\r\n		//		MainLoop();\r\n		// }\r\n		win-&gt;RunMainLoop( &amp;MainLoop );\r\n	}\r\n\r\n	// Destroy the engine instance. Destroys all the windows and engine singletons.\r\n	Engine::DestroySingleton();\r\n\r\n	// If was compiled in debug mode it will print the memory manager report\r\n	MemoryManager::ShowResults();\r\n\r\n	return EXIT_SUCCESS;\r\n}\r\n</code></pre>\r\n\r\n<h2>Im&aacute;genes</h2>\r\n\r\n<p><a href="/assets/blog/eepp-ui.png" target="_blank"><img alt="Demo de Interfaz Gráfica" src="/assets/blog/eepp-ui-thumb.jpg" /></a><br />\r\nDemo de Interfaz Gr&aacute;fica&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><a href="/assets/blog/eepp-mapeditor.png" target="_blank"><img alt="Editor de Mapas" src="/assets/blog/eepp-mapeditor-thumb.jpg" /></a><br />\r\nEditor de Mapas</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;<a href="/assets/blog/eepp-haikuos.png" target="_blank"><img alt="Corriendo en HaikuOS" src="/assets/blog/eepp-haikuos-thumb.jpg" /></a><br />\r\nCorriendo en HaikuOS&nbsp;<a href="#fn:2">2</a></p>\r\n\r\n<h2>Links</h2>\r\n\r\n<p><a href="http://code.google.com/p/eepp/" target="_blank">Reposit&oacute;rio en Google Code</a></p>\r\n\r\n<p><a href="/blog/especificaciones-de-entropia-engine-" target="_blank">Especificaciones T&eacute;cnicas</a></p>\r\n\r\n<hr />\r\n<ol>\r\n	<li>\r\n	<p><a href="http://es.wikipedia.org/wiki/MIT_License" target="_blank">Licencia MIT en Wikipedia</a>&nbsp;<a href="#fnref:1">↩</a></p>\r\n	</li>\r\n	<li>\r\n	<p><a href="http://haiku-os.org/" target="_blank">HaikuOS</a>&nbsp;<a href="#fnref:2">↩</a></p>\r\n	</li>\r\n</ol>\r\n', 'entropia-engine', '2012-04-06 04:34:58', '2012-04-06 04:34:58', 1, 0),
(32, 1, 'Test', '<p>Test</p>\r\n', 'test', '2015-05-29 00:46:24', '2015-05-29 00:46:24', 1, 1),
(33, 1, 'Test 2', '<p>Test 2</p>\r\n', 'test-2', '2015-05-30 01:07:32', NULL, 1, 1),
(34, 1, 'Test 3', '<p>Test 3</p>\r\n', 'test-3', '2015-05-30 01:07:40', NULL, 1, 1),
(35, 1, 'Test 4', '<p>Test 4</p>\r\n', 'test-4', '2015-05-30 01:07:47', NULL, 1, 1),
(36, 1, 'Test 5', '<p>Test 5</p>\r\n', 'test-5', '2015-05-30 01:08:05', NULL, 1, 1),
(37, 1, 'Test 6', '<p>Test 6</p>\r\n', 'test-6', '2015-05-30 01:08:16', NULL, 1, 1),
(38, 1, 'Test 7', '<p>Test 7</p>\r\n', 'test-7', '2015-05-30 01:08:34', NULL, 1, 1),
(39, 1, 'Test 8', '<p>Test 8</p>\r\n', 'test-8', '2015-05-30 01:08:43', NULL, 1, 1),
(40, 1, 'Test 9', '<p>Test 9</p>\r\n', 'test-9', '2015-05-30 01:08:52', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE IF NOT EXISTS `post_tags` (
  `ptag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ptag_post_id` int(10) unsigned NOT NULL,
  `ptag_name` varchar(64) NOT NULL,
  PRIMARY KEY (`ptag_id`),
  KEY `ptag_post_id` (`ptag_post_id`,`ptag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_email` varchar(64) NOT NULL,
  `user_url` varchar(100) NOT NULL,
  `user_nickname` varchar(64) NOT NULL,
  `user_display_name` varchar(128) DEFAULT NULL,
  `user_firstname` varchar(64) NOT NULL,
  `user_lastname` varchar(64) NOT NULL,
  `user_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_lastlogin` timestamp NULL DEFAULT NULL,
  `user_level` int(11) NOT NULL DEFAULT '0',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `user_session_token` varchar(64) DEFAULT NULL,
  `user_bio` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_password`, `user_email`, `user_url`, `user_nickname`, `user_display_name`, `user_firstname`, `user_lastname`, `user_registered`, `user_lastlogin`, `user_level`, `user_status`, `user_session_token`, `user_bio`) VALUES
(1, 'admin', '1be32e923d8956de2472f93317c98dfd0dda54c31d12c0248804f45cdf5a86fe', 'admin@ciblog', '', 'Prognoz', 'admin', 'Martín Lucas', 'Golini', '0000-00-00 00:00:00', '2015-06-02 05:44:52', 1000, 0, '2ff270d868ee95d8bec829075dfc75dc30c302d5f8da1077d843ff14e7853425', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
