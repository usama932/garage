<div class="card-datatable table-responsive">
	<table id="clients" class="datatables-demo table table-striped table-bordered">
		<tbody>
		<tr>
			<td>Title</td>
			<td>{{$tyre->title}}</td>
		</tr>
       
		<tr>
			<td>Tyre Width</td>
			<td>{{$tyre->tyre_width->title}}</td>
		</tr>
       		
		<tr>
			<td>Created at</td>
			<td>{{$tyre->created_at}}</td>
		</tr>
		
		</tbody>
	</table>
</div>
