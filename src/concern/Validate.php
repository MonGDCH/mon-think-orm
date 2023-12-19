<?php

declare(strict_types=1);

namespace mon\thinkOrm\concern;

use mon\util\Container;
use mon\util\Validate as UtilValidate;

/**
 * 验证器
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
trait Validate
{
    /**
     * 获取实例化验证器
     *
     * @param string $validate  验证器名称
     * @return UtilValidate
     */
    public function UtilValidate(string $validate = ''): UtilValidate
    {
        $validate = $validate ?: (property_exists($this, 'validate') && is_string($this->validate) ? $this->validate : UtilValidate::class);
        return Container::instance()->make($validate);
    }
}
