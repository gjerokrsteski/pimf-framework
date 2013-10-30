<?php
namespace Fixture;

use Pimf\Util\Enum;

class WeekDays extends Enum
{
  const __default = self::Monday;
  const Monday    = 1;
  const Tuesday   = 2;
  const Wednesday = 3;
  const Thursday  = 4;
  const Friday    = 5;
  const Saturday  = 6;
  const Sunday    = 7;
}

class State extends Enum
{
  const __default = self::Active;
  const Active    = 1;
  const Inactive  = 2;
  const Deleted   = 3;
}
