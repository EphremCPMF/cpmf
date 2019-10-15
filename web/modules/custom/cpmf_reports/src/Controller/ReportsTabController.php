<?php

namespace Drupal\cpmf_reports\Controller;

use Symfony\Component\HttpFoundation\Response;

class ReportsTabController {

  public function index() {
    return new Response('index');
  }

  public function tab_national() {
    return new Response('tab_national');
  }

  public function tab_national_pillar() {
    return new Response('tab_national_pillar');
  }

  public function tab_national_indicator() {
    return new Response('tab_national_indicator');
  }

  public function tab_cities() {
    return new Response('tab_cities');
  }

  public function tab_cities_pillar() {
    return new Response('tab_cities_pillar');
  }

  public function tab_cities_indicator() {
    return new Response('tab_cities_indicator');
  }

  public function tab_regions() {
    return new Response('tab_regions');
  }

  public function tab_regions_pillar() {
    return new Response('tab_regions_pillar');
  }

  public function tab_regions_indicator() {
    return new Response('tab_regions_indicator');
  }

  public function tab_zones() {
    return new Response('tab_zones');
  }

  public function tab_zones_pillar() {
    return new Response('tab_zones_pillar');
  }

  public function tab_zones_indicator() {
    return new Response('tab_zones_indicator');
  }

}