<?php

namespace AppBundle\Classes;

class Fruit {
  private $name;
  private $origin;
  private $eatable;
  private $wiki;

  public function __construct($name, $origin, $eatable, $wiki) {
    $this->setName($name);
    $this->setOrigin($origin);
    $this->setEatable($eatable);
    $this->setWiki($wiki);
  }
  public function getName() {return $this->name;}
  public function getOrigin() {return $this->origin;}
  public function getEatable() {return $this->eatable;}
  public function getWiki() {return $this->wiki;}

  public function setName($name) {$this->name = $name; return $this->name;}
  public function setOrigin($origin) {$this->origin = $origin; return $this->origin;}
  public function setEatable($eatable) {$this->eatable = $eatable; return $this->eatable;}
  public function setWiki($wiki) {$this->wiki = $wiki; return $this->wiki;}

}
 ?>
