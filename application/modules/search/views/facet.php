<form id="questionFilters">
	<div class="filters">
		<?php
		//_p($appliedFilters);die;
		foreach ($facets as $facetName => $facetData) { 
			if(!empty($facetData)){
			?>
				<div><b><?php echo $facetName; ?></b></div>
				<div>
					<ul style="list-style:none;max-height:200px;overflow:auto;padding:0px !important">
						<?php foreach ($facetData as $facet) { 
							if($facet['count'] > 0){ ?>
							<li>
								<input type="checkbox" id="" name="<?php echo $facetName;?>" value="<?php echo $facet['id']?>" style="float:left" <?php  
								echo isset($appliedFilters[$facetName][$facet['id']]) ? 'checked':'';
								
								?> >
								<label for="">
									<p><?php echo $facet['name'];?> <em>(<?php echo $facet['count'];?>)</em></p>
								</label>
							</li>
						<?php }} ?>
					</ul>
				</div>
		<?php }} ?>
	</div>
</form>