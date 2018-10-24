class Post {
    constructor(id, date, status, title, link, parent, order, price, hasChildren, imageUrl){
        this.id = id;
        this.date = date;
        this.status = status;
        this.title = title;
        this.link = link;
        this.parent = parent;
        this.order = order;
        this.price = price;
        this.hasChildren = hasChildren;
        this.imageUrl = imageUrl;
    }
} 
let app = new Vue({
    el: '.servicos-orcamento',
    data: {
        message : 'Olá',
        loading: 'Carregando',
        posts: []
    },
    created: function(){
        this.debounceGetPosts = _.debounce(this.getPosts, 500);
        this.debounceGetPosts();
    },
    methods: {
        getPosts: function(){
            let vm = this;
            axios.get('/wp-json/wp/v2/servicos_orcamento?_embed')
                .then(function(response){
                    posts = vm.handlePosts(response.data);
                    vm.posts = posts;
                    vm.loading = 'Carregado';
                })
                .catch(function(error){
                    console.log(error);
                    vm.loading = 'Erro';
                })
        },
        handlePosts: function(posts){
            postsList = [];
            if(Array.isArray(posts)){
                posts.forEach(function(item){
                    p = new Post();
                    p.id = item.id;
                    p.date = item.date;
                    p.status = item.status;
                    p.title = item.title.rendered;
                    p.link = item.link;
                    p.parent = item.parent;
                    p.order = item.menu_order;
                    p.price = item.servico_orcamento_preco;
                    p.hasChildren = item.has_children;
                    p.imageUrl = '';
                    if(item._embedded !== undefined){
                        if(item._embedded['wp:featuredmedia'] !== undefined){
                            p.imageUrl = item._embedded['wp:featuredmedia'][0].source_url;
                        }
                    }
                    postsList.push(p);
                });
                return postsList;
            }
            return posts;
        }
    }
});

Vue.component('post', {
    props: ['post'],
    template: `
        <div class="card">
            <template v-if="post.imageUrl !== ''">
                <img class="card-img-top" v-bind:src="post.imageUrl" alt="Card image cap">
            </template>
            <div class="card-body">
                <h5 class="card-title" v-html="post.title"></h5>
                <p class="card-text" v-html="post.price"></p>
                <a href="#" class="btn btn-primary" v-bind:href="post.link" >Ir para</a>
            </div>
        </div>
    `
});