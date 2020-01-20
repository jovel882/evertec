<?php

namespace App;

use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Spatie\Permission\Models\Permission;

class MenuFilter implements FilterInterface
{
    public function transform($item, Builder $builder)
    {
        if (auth()->user()) {
            if (in_array('SuperAdministrator', auth()->user()->getRoles())) {
                return $item;
            }
            $userPermissions=auth()->user()->getPermissions();
            if (isset($item['permission'])) {
                $bool_authorise=false;
                foreach ($item['permission'] as $permission) {
                    if (in_array($permission, $userPermissions)) {
                        $bool_authorise=true;
                        break;
                    }
                }
                if ($bool_authorise == true) {
                    return $item;
                } else {
                    return false;
                }
            } else {
                return $item;
            }
        } else {
            return false;
        }
    }
    public static function getMenu()
    {
        return \Cache::rememberForever('menu', function () {
            return [
                ['header' => 'Menu Principal'],
                [
                    'text' => 'Ordenes',
                    'url' => route('orders.index'),
                    'icon' => 'fas fa-fw fa-file-alt',
                ],
            ];
        });
    }
}
