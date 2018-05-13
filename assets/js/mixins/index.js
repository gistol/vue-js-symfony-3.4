const Mixins = {
    methods: {
        getArticles() {
            fetch('/vue-js-symfony-3.4/web/app_dev.php/articles')
                .then((res) => res.json())
                .then((data) => {
                    if (data.length > 0) {
                        this.articles = data;
                    }
                })
                .catch((err) => {
                    console.log('Erreur : ' + err)
                })
        }
    }
};

export default Mixins;