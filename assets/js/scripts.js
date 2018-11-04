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
    el: '.orcamento',
    data: {
        message : 'Olá',
        loading: 'Carregando',
        posts: [],
        prevPosts: [],
        posPosts: 0,
        selectedPosts: [],
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
                    let posts = vm.handlePosts(response.data);
                    if(posts.length > 0){
                        let ordernedPosts = vm.buildHierarchy(posts);
                        vm.posts = ordernedPosts;
                    }
                    vm.loading = 'Carregado';
                })
                .catch(function(error){
                    console.log(error);
                    vm.loading = 'Erro';
                })
        },
        buildHierarchy: function(arr) {

            let roots = [];
            let children = {};
        
            // find the top level nodes and hash the children based on parent
            for (let i = 0, len = arr.length; i < len; ++i) {
                let item = arr[i],
                    p = item.parent,
                    target = !p ? roots : (children[p] || (children[p] = []));
        
                target.push(item);
            }
        
            // function to recursively build the tree
            let findChildren = function(parent) {
                if (children[parent.id]) {
                    parent.children = children[parent.id];
                    for (let i = 0, len = parent.children.length; i < len; ++i) {
                        findChildren(parent.children[i]);
                    }
                }
            };
        
            // enumerate through to handle the case where there are multiple roots
            for (let i = 0, len = roots.length; i < len; ++i) {
                findChildren(roots[i]);
            }
        
            return roots;
        },
        handleState: function(post){
            if(post.children.length > 0){
                this.changeState(post.children);
            } else {
                this.chooseService(post);
            }
        },
        changeState: function(param){
            this.prevPosts.push(this.posts);
            this.posPosts++;
            this.posts = param;
        },
        previous: function(index){
            if(this.prevPosts.length > 0){
                this.posPosts = index || this.posPosts - 1;
                this.posts = this.prevPosts[this.posPosts];
                if(this.posPosts == 0){
                    this.clearPrevPosts();
                }
            }
        },
        clearPrevPosts: function(){
            this.prevPosts = [];
        },
        chooseService: function(post){
            this.selectedPosts.push(post);
            this.previous(0);
            console.log(this.selectedPosts);
        },
        removePost: function(index){
            if(this.selectedPosts[index] !== undefined){
                this.selectedPosts.splice(index, 1);
            }
        },
        sumPostsPrice: function(){
            if(this.selectedPosts.length > 0){
                return this.selectedPosts.reduce(function(prevVal, item){
                    return prevVal + (item.price * 1);
                }, 0);
            }
            return 0;
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
                    p.children = [];
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

Vue.component('selected-services', {
    props:['posts', 'removePost', 'sumPostsPrice'],
    template: `
        <div>
            <ul>
                <li v-for="(post, index) in posts"><span v-html="post.title"></span> <span v-html="post.price"></span><a href="#" class="btn btn-danger" @click="removePost(index)">Remover</a></li>
            </ul>
            <span v-html="sumPostsPrice()"></span>
        </div>
    `
});

Vue.component('post', {
    props: ['post', 'handleState', 'chooseService'],
    template: `
        <a class="link" v-bind:title="post.title" href="#" @click="handleState(post)">
            <template v-if="post.imageUrl !== ''">
                <img class="card-img-top" v-bind:src="post.imageUrl" alt="Card image cap">
            </template>
        </a>
    `
});