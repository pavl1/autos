/** import external dependencies */
import 'jquery';
import 'bootstrap';

/** import local dependencies */
import Router from './util/Router';
import common from './routes/common';
import home from './routes/home';
import aboutUs from './routes/about';
import series from './routes/series';
import groups from './routes/groups';
import illustration from './routes/illustration';

/**
 * Populate Router instance with DOM routes
 * @type {Router} routes - An instance of our router
 */
const routes = new Router({
  /** All pages */
  common,
  /** Home page */
  home,
  /** About Us page, note the change from about-us to aboutUs. */
  aboutUs,
  series,
  groups,
  illustration,
});

/** Load Events */
jQuery(document).ready(() => routes.loadEvents());
