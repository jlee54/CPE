<html>
 <head>
  <title>CPE Excercise</title>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css" />

 	<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
 	<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js" integrity="sha512-bZS47S7sPOxkjU/4Bt0zrhEtWx0y0CRkhEp8IckzK+ltifIIE9EMIMTuT/mEzoIMewUINruDBIR/jJnbguonqQ==" crossorigin="anonymous"></script>
 	<script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js"></script>

	<script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue-icons.min.js"></script>
 </head>
 <body>
   <div id="app" class="container-fluid">
   		<span> {{ error }}</span>
   		<div class="parties" >
	    	<b-card
	    		v-for="(party, name) in parties"
			    :title=name
			    :img-src=party.img_src
			    :img-alt=name
			    img-top
			    tag="article"
			    class="party mb-2"
			  >
			    <b-card-text>
			    	<h5>Candidate Spending:</h5>
			    	<div>Average: ${{ formatNumber(party.mean_spending) }} </div>
			    	<div>Medium: ${{ formatNumber(party.medium_spending) }} </div>
			    	<div>Total: ${{ formatNumber(party.total_spending) }} </div>
			    </b-card-text>
		  	</b-card>
		</div>
   		<div class="table-wrapper table-responsive">
       		<b-table striped hover 
       				:fields=table.fields
       				:items=table.data
       				sticky-header="60vh"
       				responsive="sm"
       		></b-table>
       	</div>
   </div>

   <script>
   	let app = new Vue({
	  el: '#app',
	  data: {
	  	error: null,
	    table: {
	    	data: [],
	    	fields: [],
	    },
	    parties: [],
	  },
	  created() {
	  	axios.get("http://127.0.0.1:8000/readCSV.php")
			.then(response => {
				if (!response.error) {
					this.parties = response.data.parties;
					this.table.data = response.data.data;

					// Construct table fields
					for (let i = 0; i < response.data.fields.length; i++) {
						let name = response.data.fields[i];

						let field = {
							"key": name,
							"sortable": true
						}
						this.table.fields.push(field);
					}
				} else {
					this.error = response.data.error;
				}
			})
	  }
	})

	function formatNumber(x) {
	    return x.toLocaleString('en-CA', {maximumFractionDigits:2});
	}

   </script>

   <style>
   		#app {
   			margin-top: 50px;
   		}

   		.parties .party {
   			margin: 10px;
   			width: 300px;
   		}

   		.parties .party img {
   			height: 320px;
   		}

   		.table-wrapper table {
   			max-width: 1350px;
	  		max-height: 50vh;
		    display: block;
		    overflow-y: scroll;
   		}

   		.table-wrapper th{
   			white-space: nowrap;
   		}

   		@media only screen and (min-width: 1024px) {
   			.parties {
	   			display: flex;
	   			justify-content: center;
	   			margin: 50px;
	   		}

	   		.table-wrapper {
	   			display: flex;
	   			justify-content: center;
	   		}
   		}
   </style>
 </body>
 <footer>
 </footer>
</html>