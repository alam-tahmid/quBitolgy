DROP TABLE IF EXISTS `#__ganalytics_profiles`;
CREATE TABLE IF NOT EXISTS `#__ganalytics_profiles` (
  `id` int(11) NOT NULL auto_increment,
  `accountID` varchar(100) NOT NULL,
  `accountName` varchar(100) NOT NULL,
  `profileID` varchar(100) NOT NULL,
  `profileName` varchar(100) NOT NULL,
  `webPropertyId` varchar(100) NOT NULL,
  `startDate` DATE NOT NULL,
  `token` text DEFAULT NULL,
  PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `#__ganalytics_stats`;
CREATE TABLE IF NOT EXISTS `#__ganalytics_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `column` int(20) NOT NULL DEFAULT '0',
  `position` int(20) NOT NULL DEFAULT '0',
  `type` varchar(250) NOT NULL DEFAULT 'list',
  `name` varchar(100) NOT NULL,
  `metrics` varchar(250) NOT NULL,
  `dimensions` varchar(250) NOT NULL,
  `sort` varchar(250) NOT NULL,
  `filter` varchar(250) DEFAULT NULL,
  `max_result` int(20) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `#__ganalytics_stats` (`id`, `group_id`, `column`, `position`, `type`, `name`, `metrics`, `dimensions`, `sort`, `filter`, `max_result`) VALUES
(1, 2, 0, 0, 'chart', 'Visitors per day', 'ga:visits,ga:newVisits', 'ga:date', 'ga:date', '', 1000),
(2, 3, 0, 0, 'list', 'Top pages', 'ga:newVisits,ga:visits,ga:bounces', 'ga:pagePath', '-ga:visits', '', 10),
(3, 4, 0, 0, 'chart', 'Referring Sites', 'ga:visits', 'ga:source', '-ga:visits', '', 10),
(4, 5, 0, 1, 'list', 'Countrys', 'ga:newVisits,ga:visits', 'ga:country', '-ga:visits', '', 300),
(5, 6, 0, 0, 'chart', 'Browsers', 'ga:visits', 'ga:browser', '-ga:visits', '', 10),
(7, 2, 0, 1, 'chart', 'Time on Site', 'ga:timeOnPage,ga:avgTimeOnPage', 'ga:date', 'ga:date', '', 1000),
(8, 2, 1, 1, 'chart', 'Page views per day', 'ga:pageviews,ga:uniquePageviews,ga:pageviewsperVisit', 'ga:date', 'ga:date', '', 1000),
(9, 2, 1, 0, 'chart', 'New vs returning', 'ga:visitors', 'ga:visitorType', '', '', 1000),
(10, 3, 1, 1, 'list', 'Page load time', 'ga:pageLoadTime', 'ga:pagePath', '-ga:pageLoadTime', '', 1000),
(11, 4, 1, 1, 'chart', 'Referring medium', 'ga:visits', 'ga:medium', '-ga:visits', '', 1000),
(12, 4, 0, 2, 'list', 'Search keywords', 'ga:newVisits,ga:visits', 'ga:keyword', '-ga:visits', '', 10),
(13, 4, 1, 3, 'chart', 'Campaign', 'ga:visits', 'ga:campaign', '-ga:visits', '', 10),
(14, 5, 0, 0, 'chart', 'Country', 'ga:visits', 'ga:country', '-ga:visits', '', 100),
(15, 5, 1, 2, 'chart', 'Continent', 'ga:visits,ga:newVisits', 'ga:continent', '-ga:visits', '', 10),
(16, 5, 1, 3, 'chart', 'Network', 'ga:visits,ga:newVisits', 'ga:networkDomain', '-ga:visits', '', 10),
(17, 6, 1, 1, 'chart', 'OS', 'ga:visits', 'ga:operatingSystem', '-ga:visits', '', 10),
(18, 6, 0, 2, 'chart', 'Screen resolution', 'ga:visits', 'ga:screenResolution', '-ga:visits', '', 10),
(19, 6, 1, 3, 'chart', 'Mobile', 'ga:visits', 'ga:isMobile', '', '', 10),
(20, 3, 0, 2, 'chart', 'Language', 'ga:visits', 'ga:language', '-ga:visits', '', 10),
(21, 3, 1, 3, 'list', 'Landing page', 'ga:newVisits,ga:visits', 'ga:landingPagePath', '-ga:visits', '', 10),
(23, 1, 0, 1, 'chart', 'Visitors per day', 'ga:visits,ga:newVisits', 'ga:date', 'ga:date', '', 1000),
(24, 1, 1, 2, 'chart', 'Country', 'ga:visits', 'ga:country', '-ga:visits', '', 10),
(25, 1, 0, 3, 'chart', 'Source', 'ga:visits', 'ga:source', '-ga:visits', '', 10),
(26, 1, 1, 4, 'list', 'Top pages', 'ga:newVisits,ga:visits,ga:bounces', 'ga:pagePath', '-ga:visits', '', 10),
(27, 7, 0, 1, 'chart', 'Server connection', 'ga:serverConnectionTime,ga:serverResponseTime', 'ga:date', 'ga:date', '', 1000),
(28, 7, 0, 2, 'chart', 'Country', 'ga:pageDownloadTime', 'ga:country', '-ga:pageDownloadTime', '', 1000),
(29, 7, 1, 3, 'chart', 'Browser', 'ga:avgPageLoadTime', 'ga:browser', '', '', 10),
(30, 7, 1, 4, 'chart', 'Average speed time', 'ga:avgPageDownloadTime,ga:avgServerConnectionTime,ga:avgServerResponseTime', 'ga:pageTitle', '-ga:avgPageDownloadTime', '', 10);

DROP TABLE IF EXISTS `#__ganalytics_stats_groups`;
CREATE TABLE IF NOT EXISTS `#__ganalytics_stats_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `position` int(20) NOT NULL DEFAULT '0',
  `column_count` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
);

INSERT INTO `#__ganalytics_stats_groups` (`id`, `name`, `position`, `column_count`) VALUES
(1, 'Overview', 0, 2),
(2, 'Visitors', 0, 2),
(3, 'Pages', 0, 2),
(4, 'Sources', 0, 2),
(5, 'Demographics', 0, 2),
(6, 'System', 0, 2),
(7, 'Speed', 0, 2);
