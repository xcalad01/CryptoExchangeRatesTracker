<template>
    <div>
        <h1>Crypto Hist Value/Ohlc</h1>
        <form @submit.prevent="addPost">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Start:</label>
                        <input type="text" class="form-control" v-model="post.start">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>End:</label>
                        <input type="text" class="form-control" v-model="post.end">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Exchange:</label>
                        <input type="text" class="form-control" v-model="post.exchange">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Convert:</label>
                        <input type="text" class="form-control" v-model="post.convert">
                    </div>
                </div>
            </div><br />

            <select v-model="post.action">
                <option disabled value="">Please select one</option>
                <option>value</option>
                <option>ohlc</option>
            </select>

            <div class="form-group">
                <button class="btn btn-primary">Query</button>
            </div>
            <pre>{{ content | pretty }}</pre>
        </form>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                content:null,
                post:{}
            }
        },
        methods: {
            addPost(){
                console.log(this.post.start);
                let uri = "http://localhost:8000/api/crypto_historical/" + this.post.action + "/" + this.post.start + "/" + this.post.end + "/" + this.post.exchange + "/" + this.post.convert;
                console.log(uri);
                this.content = this.axios.get(uri).then(response => (this.content = response.data))
            }
        },
        filters: {
            pretty: function(value) {
                console.log(value);
                return JSON.stringify(value, null, 4);
            }
        }
    }
</script>s

