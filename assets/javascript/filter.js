import { debounce } from "lodash";

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
        this.page = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
        this.moreNav = this.page === 1 && !this.content.querySelector('#product-no-content');

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

        this.form.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('keyup', debounce(this.loadForm.bind(this), 300));
        });

        this.form.querySelectorAll('input[type="checkbox"]').forEach(input => {
            input.addEventListener('change', debounce(this.loadForm.bind(this), 700));
        });

        if (this.moreNav) {
            this.pagination.innerHTML = '<button class="btn btn-primary btn-show-more">Voir plus</button>'
            this.pagination.querySelector('.btn-show-more').addEventListener('click', this.loadMore.bind(this));
        } else {
            this.pagination.addEventListener('click', clickListener);
        }


        if (this.moreNav) {
            document.addEventListener('scroll', function (e) {
                console.error(window.scrollY, document.body.scrollHeight - 1000);

                if (window.scrollY <= document.body.scrollHeight - 500) {
                    this.loadMore.bind(this);
                }
            });
        }
    }

    loadMore() {
        // On incrémente le numéro de la page
        this.page++;

        // On récupère l'URL relative de la page
        const url = new URL(window.location.href);

        // On créé un objet URLSearchParams pour manipuler plus facilement les paramètres GET
        const params = new URLSearchParams(url.search);

        // On redéfini le numéro de la page
        params.set('page', this.page);

        // On envoie la requête AJAX
        return this.loadUrl(url.pathname + '?' + params.toString(), true);
    }

    /**
     * Effectue la récupération de données du formulaire et envoie un call Ajax
     */
    async loadForm() {
        // On redéfinit la page numéro 1
        this.page = 1;

        // On créé un objet URLSearchParams pour manipuler plus facilement les paramètres GET
        const params = new URLSearchParams();
        params.set('page', this.page);

        // On récupère les inputs du form avec leur valeur
        const formData = new FormData(this.form);

        // On boucle sur les inputs du formulaire et pour chaque input
        // On ajoute une paire clé/valeur au GET
        formData.forEach((value, key) => {
            params.append(key, value);
        });

        // On récupère l'URL relative de la page
        const url = new URL(window.location.href);

        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    /**
     * Permet d'envoyer une requête ajax sur le serveur
     * 
     * @param {string} url 
     * @param {boolean} append
     */
    async loadUrl(url, append = false) {
        // Afficher le loader
        this.form.classList.add('is-loading');
        this.form.querySelector('.js-loading').style.display = 'block';

        if (this.moreNav) {
            this.pagination.querySelector('.btn-show-more').setAttribute('disabled', 'disabled');
        }

        // Créer un objet URLSearchParams
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', 1);

        // On envoie la requête AJAX
        const response = await fetch(url.split('?')[0] + '?' + params.toString());
        // const response = await fetch(`${url.split('?')[0]}?${params.toString()}`);

        if (response.ok) {
            const data = await response.json();

            // On met à jour le contenu avec les données du server
            if (append) {
                this.content.innerHTML += data.content;
            } else {
                this.content.innerHTML = data.content;
            }

            this.sorting.innerHTML = data.sorting;
            this.count.innerHTML = data.count;

            console.error(this.page, data.pages);

            if (!this.moreNav) {
                this.pagination.innerHTML = data.pagination;
            } else if (this.page == data.pages || this.content.querySelector('#product-no-content')) {
                this.pagination.classList.replace('d-flex', 'd-none');
            } else {
                this.pagination.classList.replace('d-none', 'd-flex');
            }

            params.delete('ajax');

            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
        }

        this.form.classList.remove('is-loading');
        this.form.querySelector('.js-loading').style.display = 'none';

        if (this.moreNav) {
            this.pagination.querySelector('.btn-show-more').removeAttribute('disabled');
        }
    }
}