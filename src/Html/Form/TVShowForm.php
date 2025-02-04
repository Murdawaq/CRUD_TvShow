<?php

declare(strict_types=1);

namespace Html\Form;

use Entity\Exception\ParameterException;
use Html\StringEscaper;
use Entity\TVShow;

class TVShowForm
{
    use StringEscaper;
    private ?TVShow $tvshow;

    public function __construct(?TVShow $tvshow = null)
    {
        $this->tvshow = $tvshow;
    }

    public function getTVShow()
    {
        return $this?->tvshow;
    }

    /**
     * Generates an HTML form for a TVShow object.
     *
     * @param string $action The URL where the form data will be sent when the form is submitted.
     * @return string The generated HTML form as a string.
     */
    public function getHtmlForm(string $action): string
    {

        $tvShow = $this->getTVShow();
        $tvShowName = $this->escapeString($tvShow?->getName());
        $tvShowOriginalName = $this->escapeString($tvShow?->getOriginalName());
        $tvShowHomepage = $this->escapeString($tvShow?->getHomepage());
        $tvShowOverview = $this->escapeString($tvShow?->getOverview());
        $tvShowPosterId = $tvShow?->getPosterId();


        $form = <<<HTML
<h1>Ajouter une Série</h1>
<div class="form">
    <form method="post" action="{$action}">
        <input type="hidden" name="id" value="{$tvShow?->getId()}" />
        <div class="form__group">
            <label for="name">Nom de la série</label>
            <input id="name" type="text" name="name" value="{$tvShowName}" required autocomplete="off" />
        </div>
        <div class="form__group">
            <label for="originalName">Nom d'origine de la série</label>
            <input id="originalName" type="text" name="originalName" value="{$tvShowOriginalName}" required autocomplete="off" />
        </div>
        <div class="form__group">
            <label for="homepage">Page d'accueil de la série</label>
            <input id="homepage" type="text" name="homepage" value="{$tvShowHomepage}" required autocomplete="off" />
        </div>
        <div class="form__group">
            <label for="overview">Description</label>
            <textarea id="overview" name="overview" required autocomplete="off" placeholder="Entrez la description de la série..." rows="5">{$tvShowOverview}</textarea>
        </div>
        <button type="submit" value="submit">Save</button>
    </form>
</div>    
HTML;
        return $form;
    }

    /**
     * Method that controls the data transmitted by the form in order to not corrupt the database.
     *
     * @throws ParameterException If any of the required fields in the $_POST array are empty
     * @return void Does not return anything
     */
    public function setEntityFromQueryString(): void
    {
        $tvShowId = null;
        if (isset($_POST['id']) && ctype_digit($_POST['id'])) {
            $tvShowId = (int)$_POST['id'];
        }

        if (empty($_POST['name'])) {
            throw new ParameterException("Nom de la série manquant");
        }
        if (empty($_POST['originalName'])) {
            throw new ParameterException("Nom original de la série manquant");
        }
        if (empty($_POST['homepage'])) {
            throw new ParameterException("Page d'accueil de la série manquante");
        }
        if (empty($_POST['overview'])) {
            throw new ParameterException("Aperçu de la série manquant");
        }


        $tvShowName = $this->stripTagsAndTrim($_POST['name']);
        $tvShowOriginalName = $this->stripTagsAndTrim($_POST['originalName']);
        $tvShowHomepage = $this->stripTagsAndTrim($_POST['homepage']);
        $tvShowOverview = $this->stripTagsAndTrim($_POST['overview']);

        $this->tvshow = TVShow::create($tvShowName, $tvShowOriginalName, $tvShowHomepage, $tvShowOverview, null, $tvShowId);
    }

}
