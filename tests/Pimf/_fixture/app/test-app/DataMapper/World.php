<?php
namespace Fixture\DataMapper;

use Pimf\DataMapper\Base;

class World extends Base
{
  public function find($id)
  {
    return array($id);
  }
}
