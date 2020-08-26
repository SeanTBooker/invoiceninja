<?php

namespace Tests\Feature\PdfMaker;

use App\Services\PdfMaker\Designs\Plain;
use App\Services\PdfMaker\PdfMaker;
use Tests\TestCase;

class PdfMakerTest extends TestCase
{
    public $state = [
        'template' => [],
        'variables' => [
            'labels' => [],
            'values' => [],
        ],
    ];

    public function testDesignLoadsCorrectly()
    {
        $this->markTestSkipped('STUB broken tests');

        $maker = new PdfMaker($this->state);

        $maker->design(ExampleDesign::class);

        $this->assertInstanceOf(ExampleDesign::class, $maker->design);
    }

    public function testHtmlDesignLoadsCorrectly()
    {
        $this->markTestSkipped('STUB broken tests');

        $maker = new PdfMaker($this->state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $this->assertStringContainsString('<!-- Business -->', $maker->getCompiledHTML());
    }

    public function testGetSectionUtility()
    {
        $this->markTestSkipped('STUB broken tests');

        $maker = new PdfMaker($this->state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $this->assertEquals('table', $maker->getSectionNode('product-table')->nodeName);
    }

    public function testTableAttributesAreInjected()
    {
        $this->markTestSkipped('STUB broken tests');

        $state = [
            'template' => [
                'product-table' => [
                    'id' => 'product-table',
                    'properties' => [
                        'class' => 'my-awesome-class',
                        'style' => 'margin-top: 10px;',
                        'script' => 'console.log(1)',
                    ],
                ],
                'header' => [
                    'id' => 'header',
                    'properties' => [
                        'class' => 'header-class',
                    ],
                ],
            ],
            'variables' => [
                'labels' => [],
                'values' => [],
            ],
        ];

        $maker = new PdfMaker($state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $this->assertStringContainsString('my-awesome-class', $maker->getSection('product-table', 'class'));
        $this->assertStringContainsString('margin-top: 10px;', $maker->getSection('product-table', 'style'));
        $this->assertStringContainsString('console.log(1)', $maker->getSection('product-table', 'script'));
    }

    public function testVariablesAreReplaced()
    {
        $this->markTestSkipped('STUB broken tests');


        $state = [
            'template' => [
                'product-table' => [
                    'id' => 'product-table',
                    'properties' => [
                        'class' => 'my-awesome-class',
                        'style' => 'margin-top: 10px;',
                        'script' => 'console.log(1)',
                    ],
                ],
                'header' => [
                    'id' => 'header',
                    'properties' => [
                        'class' => 'header-class',
                    ],
                ],
            ],
            'variables' => [
                'labels' => [],
                'values' => [
                    '$title' => 'Invoice Ninja',
                ],
            ],
        ];

        $maker = new PdfMaker($state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $this->assertStringContainsString('Invoice Ninja', $maker->getCompiledHTML());
        $this->assertStringContainsString('Invoice Ninja', $maker->getSection('header'));
    }

    public function testElementContentIsGenerated()
    {
        $this->markTestSkipped('STUB broken tests');


        $state = [
            'template' => [
                'product-table' => [
                    'id' => 'product-table',
                    'properties' => [],
                    'elements' => [
                        ['element' => 'thead', 'content' => '', 'elements' => [
                            ['element' => 'th', 'content' => 'Company',],
                            ['element' => 'th', 'content' => 'Contact'],
                            ['element' => 'th', 'content' => 'Country', 'properties' => [
                                'colspan' => 3,
                            ]],
                        ]],
                        ['element' => 'tr', 'content' => '', 'elements' => [
                            ['element' => 'td', 'content' => '$company'],
                            ['element' => 'td', 'content' => '$email'],
                            ['element' => 'td', 'content' => '$country', 'elements' => [
                                ['element' => 'a', 'content' => 'Click here for a link', 'properties' => [
                                    'href' => 'https://github.com/invoiceninja/invoiceninja',
                                ]],
                            ]],
                        ]],
                    ],
                ],
            ],
            'variables' => [
                'labels' => [],
                'values' => [
                    '$company' => 'Invoice Ninja',
                    '$email' => 'contact@invoiceninja.com',
                    '$country' => 'UK',
                ],
            ],
        ];

        $maker = new PdfMaker($state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $compiled = 'contact@invoiceninja.com';

        $this->assertStringContainsString($compiled, $maker->getCompiledHTML());
    }

    public function testConditionalRenderingOfElements()
    {
        $this->markTestSkipped('STUB broken tests');


        $maker1 = new PdfMaker([
            'template' => [
                'header' => [
                    'id' => 'header',
                    'properties' => [],
                ],
            ],
        ]);

        $maker1
            ->design(ExampleDesign::class)
            ->build();

        $output1 = $maker1->getCompiledHTML();

        $this->assertStringContainsString('<div id="header">$title</div>', $output1);

        $maker2 = new PdfMaker([
            'template' => [
                'header' => [
                    'id' => 'header',
                    'properties' => ['hidden' => "true"],
                ],
            ],
        ]);

        $maker2
            ->design(ExampleDesign::class)
            ->build();

        $output2 = $maker2->getCompiledHTML();

        $this->assertStringContainsString('<div id="header" hidden="true">$title</div>', $output2);

        $this->assertNotSame($output1, $output2);
    }

    public function testOrderingElements()
    {
        $this->markTestSkipped('STUB broken tests');


        $maker = new PdfMaker([
            'template' => [
                'header' => [
                    'id' => 'header',
                    'properties' => [],
                    'elements' => [
                        ['element' => 'h1', 'content' => 'h1-element'],
                        ['element' => 'span', 'content' => 'span-element'],
                    ]
                ],
            ],
        ]);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $node = $maker->getSectionNode('header');

        $before = [];

        foreach ($node->childNodes as $child) {
            $before[] = $child->nodeName;
        }

        $this->assertEquals('h1', $before[1]);

        $maker = new PdfMaker([
            'template' => [
                'header' => [
                    'id' => 'header',
                    'properties' => [],
                    'elements' => [
                        ['element' => 'h1', 'content' => 'h1-element', 'order' => 1],
                        ['element' => 'span', 'content' => 'span-element', 'order' => 0],
                    ]
                ],
            ],
        ]);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $node = $maker->getSectionNode('header');

        $after = [];

        foreach ($node->childNodes as $child) {
            $after[] = $child->nodeName;
        }

        $this->assertEquals('span', $after[1]);
    }

    public function testGeneratingPdf()
    {
        $this->markTestSkipped('STUB broken tests');


        $state = [
            'template' => [
                'header' => [
                    'id' => 'header',
                    'properties' => ['class' => 'text-white bg-blue-600 p-2'],
                ],
                'product-table' => [
                    'id' => 'product-table',
                    'properties' => ['class' => 'table-auto'],
                    'elements' => [
                        ['element' => 'thead', 'content' => '', 'elements' => [
                            ['element' => 'tr', 'content' => '', 'elements' => [
                                ['element' => 'th', 'content' => 'Title', 'properties' => ['class' => 'px-4 py-2']],
                                ['element' => 'th', 'content' => 'Author', 'properties' => ['class' => 'px-4 py-2']],
                                ['element' => 'th', 'content' => 'Views', 'properties' => ['class' => 'px-4 py-2']],
                            ]]
                        ]],
                        ['element' => 'tbody', 'content' => '', 'elements' => [
                            ['element' => 'tr', 'content' => '', 'elements' => [
                                ['element' => 'td', 'content' => 'An amazing guy', 'properties' => ['class' => 'border px-4 py-2']],
                                ['element' => 'td', 'content' => 'David Bomba', 'properties' => ['class' => 'border px-4 py-2']],
                                ['element' => 'td', 'content' => '1M', 'properties' => ['class' => 'border px-4 py-2']],
                            ]],
                            ['element' => 'tr', 'content' => '', 'elements' => [
                                ['element' => 'td', 'content' => 'Flutter master', 'properties' => ['class' => 'border px-4 py-2']],
                                ['element' => 'td', 'content' => 'Hillel Coren', 'properties' => ['class' => 'border px-4 py-2']],
                                ['element' => 'td', 'content' => '1M', 'properties' => ['class' => 'border px-4 py-2']],
                            ]],
                            ['element' => 'tr', 'content' => '', 'elements' => [
                                ['element' => 'td', 'content' => 'Bosssssssss', 'properties' => ['class' => 'border px-4 py-2']],
                                ['element' => 'td', 'content' => 'Shalom Stark', 'properties' => ['class' => 'border px-4 py-2']],
                                ['element' => 'td', 'content' => '1M', 'properties' => ['class' => 'border px-4 py-2']],
                            ]],
                            ['element' => 'tr', 'content' => '', 'order' => 4, 'elements' => [
                                ['element' => 'td', 'content' => 'Three amazing guys', 'properties' => ['class' => 'border px-4 py-2', 'colspan' => '100%']],
                            ]],
                        ]],
                    ],
                ]
            ],
            'variables' => [
                'labels' => [],
                'values' => [
                    '$title' => 'Invoice Ninja',
                ],
            ]
        ];

        $maker = new PdfMaker($state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        $this->assertTrue(true);
    }

    public function testGetSectionHTMLWorks()
    {
        $design = new ExampleDesign();

        $html = $design
            ->document()
            ->getSectionHTML('product-table');

        $this->assertStringContainsString('id="product-table"', $html);
    }

    public function testWrapperHTMLWorks()
    {
        $design = new ExampleDesign();

        $state = [
            'template' => [
                'product-table' => [
                    'id' => 'product-table',
                    'elements' => [
                        ['element' => 'p', 'content' => 'Example paragraph'],
                    ],
                ],
            ],
            'variables' => [
                'labels' => [],
                'values' => [],
            ],
            'options' => [
                'repeat_header_and_footer' => true,
            ],
        ];

        $maker = new PdfMaker($state);

        $maker
            ->design(ExampleDesign::class)
            ->build();

        info($maker->getCompiledHTML(true));
    }
}
