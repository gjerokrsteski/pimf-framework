<?php
/**
 * Pimf
 *
 * PHP Version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * Provides a great way to build de-coupled applications and allows plug-ins to tap
 * into the core of your application without modifying the code.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Event
{
  /**
   * All registered events.
   * @var array
   */
  protected static $events = array();

  /**
   * The queued events waiting for flushing.
   * @var array
   */
  protected static $queued = array();

  /**
   * All registered queue flusher callbacks.
   * @var array
   */
  protected static $flushers = array();

  /**
   * Determine if an event has any registered listeners.
   * @param string $event
   * @return bool
   */
  public static function listeners($event)
  {
    return isset(static::$events[$event]);
  }

  /**
   * Register a callback for a given event.
   *
   * <code>
   *    // register a callback for the "start" event
   *    Pimf_Event::listen('start', function() {return 'Started!';});
   *
   *    // register an object instance callback for the given event
   *    Pimf_Event::listen('event', array($object, 'method'));
   * </code>
   *
   * @param string $event
   * @param mixed $callback
   * @return void
   */
  public static function listen($event, $callback)
  {
    static::$events[$event][] = $callback;
  }

  /**
   * Override all callbacks for a given event with a new callback.
   * @param string $event
   * @param mixed  $callback
   * @return void
   */
  public static function override($event, $callback)
  {
    static::clear($event);
    static::listen($event, $callback);
  }

  /**
   * Add an item to an event queue for processing.
   * @param string $queue
   * @param mixed $key
   * @param array $data
   */
  public static function queue($queue, $key, $data = array())
  {
    static::$queued[$queue][$key] = $data;
  }

  /**
   * Register a queue flusher callback.
   * @param string $queue
   * @param mixed $callback
   * @return void
   */
  public static function flusher($queue, $callback)
  {
    static::$flushers[$queue][] = $callback;
  }

  /**
   * Clear all event listeners for a given event.
   * @param string $event
   * @return void
   */
  public static function clear($event)
  {
    unset(static::$events[$event]);
  }

  /**
   * Fire an event and return the first response.
   *
   * <code>
   *    // fire the "start" event
   *    $response = Pimf_Event::first('start');
   *
   *    // fire the "start" event passing an array of parameters
   *    $response = Pimf_Event::first('start', array('Pimf', 'Framework'));
   * </code>
   *
   * @param string $event
   * @param array $parameters
   * @return mixed
   */
  public static function first($event, $parameters = array())
  {
    $responses = static::fire($event, $parameters);
    return reset($responses);
  }

  /**
   * Fire an event and return the first response.
   * Execution will be halted after the first valid response is found.
   * @param string $event
   * @param array $parameters
   * @return mixed
   */
  public static function until($event, $parameters = array())
  {
    return static::fire($event, $parameters, true);
  }

  /**
   * Flush an event queue, firing the flusher for each payload.
   * @param string $queue
   * @return void
   */
  public static function flush($queue)
  {
    foreach (static::$flushers[$queue] as $flusher) {
      // spin through each payload registered for the event
      if (!isset(static::$queued[$queue])) {
        continue;
      }

      // fire the flusher, passing each payloads
      foreach (static::$queued[$queue] as $key => $payload) {
        array_unshift($payload, $key);
        call_user_func_array($flusher, $payload);
      }
    }
  }

  /**
   * Fire an event so that all listeners are called.
   *
   * <code>
   *    // fire the "start" event
   *    $responses = Pimf_Event::fire('start');
   *
   *    // fire the "start" event passing an array of parameters
   *    $responses = Pimf_Event::fire('start', array('Pimf', 'Framework'));
   *
   *    // fire multiple events with the same parameters
   *    $responses = Pimf_Event::fire(array('start', 'loading'), $parameters);
   * </code>
   *
   * @param string|array $events
   * @param array $parameters
   * @param bool $halt
   * @return array|null
   */
  public static function fire($events, $parameters = array(), $halt = false)
  {
    $responses = array();

    $parameters = (array)$parameters;

    // If the event has listeners, iterate through them and call each listener,
    // passing in the parameters.
    foreach ((array)$events as $event) {

      if (static::listeners($event)) {

        foreach (static::$events[$event] as $callback) {
          $response = call_user_func_array($callback, $parameters);

          // If the event is set to halt, return the first response that is not null.
          if ($halt and !is_null($response)) {
            return $response;
          }

          $responses[] = $response;
        }
      }
    }

    return $halt ? null : $responses;
  }
}