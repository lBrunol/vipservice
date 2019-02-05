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
        message : '',
        errorMessage: '',
        loading: true,
        sendingBudget: false,
        posts: [],
        prevPosts: [],
        posPosts: 0,
        selectedPosts: [],
        step: 1,
        isAuthenticate: false,
        fields: [
            { field: 'nome', type: 'text', name: 'Nome', required: true },
            { field: 'email', type: 'text', name: 'E-mail', required: true },
            { field: 'telefone', type: 'text', name: 'Telefone', required: true },
            { field: 'mensagem', type: 'textarea', name: 'Mensagem', required: false }
        ],
        budget: {},
        discount: 0
    },
    created: function(){
        const vm = this;
        vm.debounceGetPosts = _.debounce(vm.getPosts, 500);
        vm.debounceGetPosts();
        (function(){
            $('.wpcf7 form').on('DOMNodeInserted', function (e) {
                if ($(e.target).hasClass('wpcf7-mail-sent-ok')) {
                    vm.message = $(e.target).text();
                    vm.reset();
                }
        
                if ($(e.target).hasClass('wpcf7-validation-errors') || $(e.target).hasClass('wpcf7-mail-sent-ng'))
                    $('.wpcf7-form .message').html('<div role="alert" class="alert alert-danger text-center">' + $(e.target).text() + '</div>');
            });
            setTimeout(function(){
                $('div.wpcf7 > form').each(function() {
                    var $form = $(this);
                    wpcf7.initForm($form);
        
                    if (wpcf7.cached) {
                        wpcf7.refill($form);
                    }
                });
            }, 50);
        })();
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
                    $('[data-toggle="tooltip"]').tooltip();
                    vm.loading = false;
                })
                .catch(function(error){
                    console.log(error);
                    vm.loading = false;
                })
        },
        clearForm: function(){
            this.fields.forEach(function(item){
                if(item.type == 'text')
                    $('input[name="' + item.field + '"]').val('');
                else if(item.type == 'textarea')
                    $('textarea[name="' + item.field + '"]').val('');
            });
        },
        getFormValues: function(){
            const formValues = [];

            this.fields.forEach(function(item){
                let val = '';
                if(item.type == 'text')
                    val = $('input[name="' + item.field + '"]').val();
                else if(item.type == 'textarea')
                    val = $('textarea[name="' + item.field + '"]').val();

                formValues.push({ field: item.field, value: val, name: item.name, required: item.required });
            });

            return formValues;
        },
        validateFormValues: function(formValues){
            const vm = this;
            vm.errorMessage = '';

            for(let i = 0; i < formValues.length; i++){
                if(formValues[i].value == '' && formValues[i].required === true){
                    vm.errorMessage = 'O campo ' + formValues[i].name + ' é obrigatório.';
                    return false;
                }
            }
            return true;
        },
        /**
         * Constrói o objeto 'Budget' (orçamento)
         * 
         * @param {Array} formValues 
         * @return Boolean|Array Falso caso não existam posts selecionados e Objeto caso a formação do orçamento ocorra.
         */
        buildBudget: function(formValues){
            const vm = this;
            let budget = {};

            formValues.forEach(function(item){
                budget[item.field] = item.value;
            });  

            /*if(vm.selectedPosts.length == 0){
                vm.errorMessage = 'Não existem serviços selecionados';
                return false;
            }

            budget.servicos = vm.selectedPosts.map(function(item){
                return item.id;
            });*/

            return budget;
        },
        submitForm: function(e){
            e.preventDefault();
            const vm = this;
            const formValues = vm.getFormValues();
            
            if(this.validateFormValues(formValues)){
                let budget = this.buildBudget(formValues);
                vm.message = '';
                
                if(budget){
                    this.budget = budget;
                    this.nextStep();
                }  
            }
        },
        addBudget: function(){
            const vm = this;

            if(vm.selectedPosts.length == 0){
                vm.errorMessage = 'Não existem serviços selecionados';
                return false;
            }

            vm.budget.servicos = vm.selectedPosts.map(function(item){
                return item.id;
            });

            return true;
        },
        sendBudget: function(){
            const vm = this;
            
            vm.sendingBudget = true;

            if(vm.addBudget()){
                if(this.isAuthenticate){
                    axios({
                        method: 'post',
                        url: '/wp-json/vipservice/v1/budget',
                        data: vm.budget,
                        headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then(function(response){
                        console.log(response);
                        if(response.data.ID > 0){
                            vm.message = 'Orçamento enviado com sucesso. Dentro de XX dias você receberá um retorno.';
                            vm.reset();
                        } else {
                            vm.errorMessage = 'Não foi possível enviar o orçamento. Por favor, entre em contato através do telefone (11) 9999-9999.';
                        }
                        vm.sendingBudget = false;
                    })
                    .catch(function(error){
                        console.log(error);
                        vm.errorMessage = 'Não foi possível enviar o orçamento. Por favor, entre em contato através do telefone (11) 9999-9999.';
                        vm.sendingBudget = false;
                    });
                } else {
                    vm.errorMessage = 'Não foi possível enviar o orçamento devido a problemas na autenticação. Por favor, entre em contato através do telefone (11) 9999-9999.';
                    vm.sendingBudget = false;
                }
            }
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
        authenticate: function(cb){
            let vm = this;
            if(localStorage.getItem('token') != null){
                axios({
                    method: 'post',
                    url: '/wp-json/jwt-auth/v1/token/validate',
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then(function(response){
                    vm.isAuthenticate = true;
                    if(typeof cb === 'function'){
                        cb({ status: response.data.code, token: localStorage.getItem('token') });
                    }
                    console.log(response);
                })
                .catch(function(error){
                    vm.isAuthenticate = false;
                    if(error.data.code === 'jwt_auth_invalid_token'){
                        localStorage.removeItem("token");
                        vm.authenticate(cb);
                    } else {
                        if(typeof cb === 'function'){
                            cb({ status: error.data.code });
                        }
                        console.log(error);
                    }                    
                });
            } else {
                axios.post('/wp-json/jwt-auth/v1/token', { username: 'admin', password: 'dev' })
                .then(function(response){
                    vm.isAuthenticate = true;                    
                    console.log(response);
                    if(response.data.token){
                        if(typeof cb === 'function'){
                            localStorage.setItem('token', response.data.token);
                            cb({ status: 'authenticated', token: response.data.token });
                        }
                    } else {
                        cb({ status: error.data.code });                        
                    }
                })
                .catch(function(error){
                    vm.isAuthenticate = false;
                    console.log(error);
                    if(typeof cb === 'function'){
                        cb({ status: error.data.code });
                    }
                });
            }
        },
        handleState: function(post){
            this.message = '';
            this.errorMessage = '';
            let vm = this;
            this.authenticate(function(response){
                if(vm.isAuthenticate){
                    if(post.children.length > 0){
                        vm.changeState(post.children);
                    } else {
                        vm.chooseService(post);
                    }
                } else {
                    vm.errorMessage = 'Ocorreu um erro ao recuperar os serviços. Entre em contato através do e-mail suporte@vipservice.com.br';
                }
            });
        },
        changeState: function(param){
            this.prevPosts.push(this.posts);
            this.posPosts++;
            this.posts = param;
        },
        previous: function(index){
            if(this.prevPosts.length > 0){
                if(index !== undefined && index >= 0){
                    this.posPosts = index;
                } else if(this.posPosts.length > 0) {
                    this.posPosts = this.posPosts - 1;                    
                } else {
                    this.posPosts = 0;
                }
                this.posts = this.prevPosts[this.posPosts];
                if(this.posPosts == 0){
                    this.clearPrevPosts();
                }
            }
        },
        shouldNextStep: function(){
            if(this.selectedPosts.length > 0){
                this.nextStep();
            } else {
                alert("Selecione ao menos um serviço");
            }
        },
        nextStep: function(){
            this.step = this.step + 1;            
        },
        previousStep: function(){
            this.step = this.step - 1;
        },
        clearPrevPosts: function(){
            this.prevPosts = [];
        },
        chooseService: function(post){
            this.selectedPosts.push(post);
            this.previous(0);
        },
        removePost: function(index){
            if(this.selectedPosts[index] !== undefined){
                this.selectedPosts.splice(index, 1);
            }
        },
        reset: function(){
            this.clearPrevPosts();
            this.clearForm();
            this.selectedPosts = [];
            this.posPosts = 0;
            this.step = 1;
        },
        writePostsPrice: function(){
            let sum = this.sumPostsPrice();

            if(sum > 199){
                this.discount = sum * 0.15;
            } else if(sum > 399){
                this.discount = sum * 0.2;
            } else {
                this.discount = 0;
            }

            if(!isNaN(sum)){
                sum -= this.discount;
                return sum.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
            }
            return 0;
        },
        writeDiscountPrice: function(){
            if(!isNaN(this.discount))                
                return this.discount.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
        },
        sumPostsPrice: function(){
            if(this.selectedPosts.length > 0){
                let total = this.selectedPosts.reduce(function(prevVal, item){
                    return prevVal + (item.price * 1);
                }, 0);
                return total;
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
    props:['posts', 'removePost', 'writePostsPrice', 'writeDiscountPrice', 'discount'],
    template: `
        <table class="table table-orcamento">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Preço</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(post, index) in posts">
                    <td v-html="post.title"></td> 
                    <td v-html="parseFloat(post.price).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'})"></td>
                    <td><button type="button" class="btn btn-xs btn-danger" @click="removePost(index)"><i class="icon-cancel"></i></button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr v-if="discount > 0">
                    <td><strong>Desconto</strong></td>
                    <td colspan="2"><span v-html="writeDiscountPrice()"></span></td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td colspan="2"><span v-html="writePostsPrice()"></span></td>
                </tr>
            </tfoot>
        </table>
    `
});

Vue.component('post', {
    props: ['post', 'handleState', 'chooseService'],
    template: `
        <button type="button" class="link" v-bind:title="post.title" @click="handleState(post)" data-toggle="tooltip">
            <template v-if="post.imageUrl !== ''">
                <img class="card-img-top" v-bind:src="post.imageUrl" alt="Card image cap">
            </template>
        </button>
    `
});

$(function(){
    $('.trabalhos-realizados-carousel .img-link').on('mouseenter', function(){
        if($(this).siblings('.img-link-antes').length > 0)
            $(this).addClass('hide').siblings('.img-link-antes').removeClass('hide');
    });
    $('.trabalhos-realizados-carousel .img-link-antes').on('mouseleave', function(){
        if($(this).siblings('.img-link').length > 0)
            $(this).addClass('hide').siblings('.img-link').removeClass('hide');
    });

    $('.trabalhos-realizados-carousel .link-banner').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).siblings().removeClass('js-active');
		$(this).addClass('js-active');
		if(!$('body').hasClass('floater-open'))
			openFloater();
		$('body').addClass('floater-gallery-open');
		loadFloaterImage($(this).data('img'));
	});

	$('.floater-site .floater-site-dialog').on('click', function(e){
		e.stopPropagation();
	});

	$('.floater-site.-gallery').on('floater.hide', function(){
		$('body').removeClass('floater-gallery-open');
		$('.floater-gallery .floater-gallery-image.-loader').removeClass('js-hidden');
		$('.floater-gallery .floater-gallery-image.-photo').attr('src','javascript:;').addClass('js-hidden');
		$('.trabalhos-realizados-carousel .link-banner').removeClass('js-active');
		$('.floater-gallery .js-left-gallery, .floater-gallery .js-right-gallery').removeClass('js-hidden');
	});

	$('.floater-site .close').on('click', function(e){
		e.preventDefault();
		closeFloater($(this));
	});

	var openFloater = function(){
		$('.floater-background, .floater-site').addClass('js-active');
		$('body').addClass('floater-open');
	}

	$('body').on('click', function(){
		if($(this).hasClass('floater-open'))
			closeFloater('.floater-site');
	})
	.on('keyup', function(e){
		if($(this).hasClass('floater-open') && e.keyCode == 27)
			closeFloater('.floater-site');
		if($(this).hasClass('floater-gallery-open') && e.keyCode == 37)
			$('.floater-gallery .js-left-gallery').trigger('click');
		if($(this).hasClass('floater-gallery-open') && e.keyCode == 39)
			$('.floater-gallery .js-right-gallery').trigger('click');

	});

	var closeFloater = function(instance){
		var $floater = "";

		if(typeof instance === 'undefinded'){
			return false;
		}else if(typeof instance === 'string'){
			$floater = $(instance);
		} else if(typeof instance === 'object'){
			$floater = $(instance).closest('.floater-site');
		}

		if($floater instanceof jQuery){
			$floater.trigger('floater.hidden');
			$floater.removeClass('js-active');
			setTimeout(function(){
				$('.floater-background').removeClass('js-active');
				$('body').removeClass('floater-open');
				$floater.trigger('floater.hide');
			}, 300);
		} else {
			return false;
		}
	}

	var loadFloaterImage = function(src){
		if(src !== undefined){
			$('.floater-gallery .floater-gallery-image.-loader').removeClass('js-hidden');
			$('.floater-gallery .floater-gallery-image.-photo').addClass('js-hidden');
			
			getAsyncImage(src).then(function(src){
				$('.floater-gallery .floater-gallery-image.-loader').addClass('js-hidden');
				$('.floater-gallery .floater-gallery-image.-photo').attr('src', src).removeClass('js-hidden');
			}).catch(function(){
				$('.floater-gallery .floater-gallery-message').removeClass('js-hidden');
				$('.floater-gallery .floater-gallery-image.-loader').addClass('js-hidden');
			});
		}
	}

});

function getAsyncImage(url, beforeStart){
	return new Promise(function(resolve, reject){
		var image = new Image();

		if(beforeStart !== undefined)
			beforeStart();

		image.onload = function(){
			resolve(url);
		};

		image.onerror = function(){
			reject(url);
		};
		
		image.src = url;

	});
}