<?php

namespace Villermen\Minecraft\Service;

use Villermen\Minecraft\App;

class ViewRenderer
{
    // TODO: Inject these in a smarter way (maybe through request or dedicated config service)
    protected const VIEW_ROOT = App::PROJECT_ROOT . '/view';

    // TODO: Layout
    // TODO: Use parameters

    public function renderView(string $viewFile, array $parameters = []): string
    {
        ob_start();
        include(self::VIEW_ROOT . '/' . $viewFile);
        return ob_get_clean();
    }
}
