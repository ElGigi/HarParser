<?php
/*
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2022 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace ElGigi\HarParser\Tests\Builder;

use ElGigi\HarParser\Builder\Builder;
use ElGigi\HarParser\Parser;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function test()
    {
        $parser = new Parser();
        $log = $parser->parse(__DIR__ . '/../example.har', true);

        $builder = new Builder();
        $builder->setVersion($log->getVersion());
        $builder->setCreator($log->getCreator());
        $builder->setBrowser($log->getBrowser());
        $builder->addPage(...($log->getPages() ?? []));
        $builder->addEntry(...($log->getEntries() ?? []));
        $builder->setComment($log->getComment());

        $this->assertEquals(
            json_encode($log),
            json_encode($builder->build()),
        );
    }

    public function testWithInitialLog()
    {
        $parser = new Parser();
        $log = $parser->parse(__DIR__ . '/../example.har', true);

        $builder = new Builder($log);

        $this->assertEquals(
            json_encode($log),
            json_encode($builder->build()),
        );
    }
}
