<?php namespace BoundedContext\Projector;

use BoundedContext\Contracts\Event;

trait Projecting
{

    private function from_camel_case($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    private function get_function_name(Event $e)
    {
        $reflect = new \ReflectionClass($e);
        $class_name = $reflect->getShortName();

        $namespaced_class = get_class($e);
        $words = preg_split('#[\\\.]#', $namespaced_class, -1, PREG_SPLIT_NO_EMPTY);

        return 'when_' . strtolower($words[1]) . '_' . strtolower($words[3]) . '_' . $this->from_camel_case($class_name);
    }

    private function mutate(Projectable $item)
    {
        if (!$this->can_apply($item))
        {
            return false;
        }

        $function = $this->get_function_name($item->event());

        $this->$function($this->projection, $item);

        $this->projection->increment_version();
    }

    protected function can_apply(Projectable $item)
    {
        $function = $this->get_function_name($item->event());

        return method_exists($this, $function);
    }

    protected function apply(Projectable $item)
    {
        $this->projection->increment_count();

        $this->mutate($item);

        $this->projection->set_last_id($item->id());
    }
}
