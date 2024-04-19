/**
 * Class Filter pour gérer les dynamiquement et en AJAX les filtres sur la page produits
 * 
 * @property {HTMLElement} sorting - Le composant de tri
 * @property {HTMLElement} count - Le composant de comptage
 * @property {HTMLElement} form - Le formulaire de filtre
 * @property {HTMLElement} content - Le contenu de la liste des produits
 * @property {HTMLElement} pagination - La pagination
 */
export class Filter {
    /**
     * @param {HTMLElement} element - L'élément HTML qui contient tous les composants du filtre
     */
    constructor(element) {
        // Si l'élément n'existe pas, on ne fait rien
        if (!element) {
            return;
        }

        // On récupère les composants de la page et les mettre dans des propriétés de l'objet
        this.sorting = element.querySelector('.js-filter-sorting');
        this.count = element.querySelector('.js-filter-count');
        this.form = element.querySelector('.js-filter-form');
        this.content = element.querySelector('.js-filter-content');
        this.pagination = element.querySelector('.js-filter-pagination');

        // On ajoute les évènements sur les composants
        this.bindEvent();
    }

    /**
     * Ajoute les écoutes d'évènements sur les composants du filtre
     */
    bindEvent() {
        const clickListener = (e) => {
            if (e.target.tagName === 'A') {
                e.preventDefault();

                this.loadUrl(e.target.getAttribute('href'));
            }
        }

        this.sorting.addEventListener('click', clickListener);
        this.pagination.addEventListener('click', clickListener);
    }

    /**
     * Permet d'envoyer une requête ajax sur le serveur
     * 
     * @param {string} url 
     */
    async loadUrl(url) {
        // Créer un objet URLSearchParams
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', 1);

        // On envoie la requête AJAX
        const response = await fetch(url.split('?')[0] + '?' + params.toString());
        // const response = await fetch(`${url.split('?')[0]}?${params.toString()}`);

        if (response.ok) {
            const data = await response.json();

            this.content.innerHTML = data.content;
            this.sorting.innerHTML = data.sorting;
            this.count.innerHTML = data.count;
            this.pagination.innerHTML = data.pagination;

            params.delete('ajax');

            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
        }
    }
}