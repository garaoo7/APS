<form id="questionFilters">
	<div class="filters">
		<?php
		//_p($facets);die;
		foreach ($facets as $facetName => $facetData) { ?>
				<div><b><?php echo $facetName; ?></b></div>
				<div>
					<ul style="list-style:none;height:155px;overflow:auto;padding:0px !important">
						<?php foreach ($facetData as $facet) { ?>
							<li>
								<input type="checkbox" id="" name="<?php echo $facetName;?>[]" value="<?php echo $facet['name']?>" style="float:left">
								<label for="">
									<p><?php echo $facet['name'];?> <em>(<?php echo $facet['count'];?>)</em></p>
								</label>
							</li>
						<?php } ?>
					</ul>
				</div>
		<?php } ?>
	</div>
</form>