<?php

namespace App\Routines;

use App\Contracts\Routines\Repository as RepositoryContract;
use App\Contracts\Routines\Routine;
use App\Contracts\Routines\WantsDiscord;
use App\Contracts\Routines\WantsRepository;
use Discord\Discord;
use Illuminate\Contracts\Container\Container;

class Repository implements RepositoryContract
{
    /**
     * The ids by tags.
     *
     * @var array
     */
    protected $idByTags = [];

    /**
     * The tags by id.
     *
     * @var array
     */
    protected $tagsById = [];

    /**
     * The routines by id.
     *
     * @var array
     */
    protected $routinesById = [];

    /**
     * Construct the repository.
     *
     * @param Container $con
     * @param Discord $discord
     */
    public function __construct(
        protected Container $con,
        protected Discord $discord,
    )
    { }

    /**
     * @inheritdoc
     */
    public function create($class, $parameters = []): Routine
    {
        /** @var Routine */
        $routine = $this->con->make($class, array_merge(
            [
                'repository' => $this,
                'discord' => $this->discord,
            ],
            $parameters,
        ));

        if ($routine instanceof WantsRepository) {
            $routine->withRepository($this);
        }

        if ($routine instanceof WantsDiscord) {
            $routine->withDiscord($this->discord);
        }

        $this->index($routine);

        return $routine;
    }

    /**
     * @inheritdoc
     */
    public function reindex(Routine $routine)
    {
        $this->removeById($routine->id());
        $this->index($routine);
    }

    /**
     * @inheritdoc
     */
    public function tagsExists($tags)
    {
        return ! empty($this->getMatchingIds($tags));
    }

    /**
     * @inheritdoc
     */
    public function destroyByTags($tags)
    {
        foreach ($this->getMatchingIds($tags) as $id) {
            $this->destroyById($id);
        }
    }

    /**
     * @inheritdoc
     */
    public function destroy(Routine $routine)
    {
        $this->destroyById($routine->id());
    }

    /**
     * Get all ids that match the given tags.
     *
     * @param string[] $tags
     * @return string[]
     */
    protected function getMatchingIds($tags)
    {
        $idLists = [];

        foreach ($tags as $tag) {
            $idLists[] = $this->idByTags[$tag] ?? [];
        }

        return array_values(
            array_intersect(...$idLists)
        );
    }

    /**
     * Destroy a routine by id.
     *
     * @param string $id
     * @return void
     */
    protected function destroyById($id)
    {
        $routine = $this->routinesById[$id] ?? null;

        $this->removeById($id);

        if ($routine) {
            $routine->destroy();
        }
    }

    /**
     * Destroy a routine by id.
     *
     * @param string $id
     * @return void
     */
    protected function removeById($id)
    {
        if (empty($this->tagsById[$id])) {
            return;
        }

        $tags = $this->tagsById[$id];

        unset($this->tagsById[$id]);

        foreach ($tags as $tag) {
            $this->idByTags[$tag] = array_filter(
                $this->idByTags[$tag] ?? [],
                fn ($idOfTag) => $idOfTag !== $id
            );

            if (empty($this->idByTags[$tag])) {
                unset($this->idByTags[$tag]);
            }
        }

        unset($this->routinesById[$id]);
    }

    /**
     * Index the specific routine.
     *
     * @param Routine $routine
     * @return void
     */
    protected function index(Routine $routine)
    {
        $id = $routine->id();
        $tags = $routine->tags();

        foreach ($tags as $tag) {
            $this->idByTags[$tag][] = $id;
        }

        $this->tagsById[$id] = $tags;
        $this->routinesById[$id] = $routine;
    }
}
