<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Enum;

enum CatalogSourceName: string
{
    case Products = 'products';
    case Categories = 'categories';
    case CmsPages = 'cms_pages';
}
