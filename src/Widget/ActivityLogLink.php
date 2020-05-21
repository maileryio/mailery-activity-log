<?php

namespace Mailery\Activity\Log\Widget;

use Yiisoft\Widget\Widget;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Html\Html;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;

class ActivityLogLink extends Widget
{
    /**
     * @var string|null
     */
    private ?string $tag = 'a';

    /**
     * @var string|null
     */
    private ?string $module = null;

    /**
     * @var object|null
     */
    private ?object $entity = null;

    /**
     * @var string|null
     */
    private ?string $label;

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @var string
     */
    private string $routeName = '/activity-log/default/index';

    /**
     * @var array
     */
    private array $routeParams = [];

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $tag
     * @return self
     */
    public function tag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @param string $module
     * @return self
     */
    public function module(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @param object $entity
     * @return self
     */
    public function entity(object $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @param string $label
     * @return self
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param array $options
     * @return self
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $routeName
     * @return self
     */
    public function routeName(string $routeName): self
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * @param array $routeParams
     * @return self
     */
    public function routeParams(array $routeParams): self
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function run(): string
    {
        $options = array_merge(
            [
                'href' => $this->buildHref(),
            ],
            $this->options
        );

        return Html::tag($this->tag, $this->label, $options);
    }

    /**
     * @return string
     */
    private function buildHref(): string
    {
        if (($entity = $this->entity) !== null && $entity instanceof LoggableEntityInterface) {
            $routeParams = [
                'objectId' => $entity->getObjectId(),
                'objectClass' => $entity->getObjectClass(),
            ];
        } else if ($this->module !== null) {
            $routeParams = [
                'module' => $this->module,
            ];
        } else {
            $routeParams = [];
        }

        return $this->urlGenerator->generate($this->routeName, array_merge($routeParams, $this->routeParams));
    }
}
