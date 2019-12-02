<?php

/*
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\Cache;

/**
 */
interface DirectoryInterface
{
    /**
     * @param string $file
     *
     * @return string
     */
    public function getRelativePathTo($file);
}
