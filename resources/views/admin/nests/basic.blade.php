<div class="info-box-content">
	<span class="info-box-text">下级总数</span>
	<span class="info-box-number">
		{{ count($nest->children) + count($nest->children->pluck('children')->flatten()->toArray()) }}
	</span>
</div>