<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: default.php 170 2014-01-26 06:34:40Z thongta $
 * @author               thimpress.com
 * @copyright            2014 thimpress.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
?>
<script>
	angular.module('sortApp', [])
		.controller('mainController', function($scope, $sce) {
		$scope.$sce			= $sce;
		$scope.addon_status = 0;
		$scope.addon_type	= 'all'
		$scope.sortType     = 'name'; // set the default sort type
		$scope.sortReverse  = false;  // set the default sort order
		$scope.searchFish   = '';     // set the default search/filter term

		// create the list of sushi rolls 
		$scope.plugins_avaiable = <?php _e(json_encode($this->data['avaiable'])); ?>;
		$scope.renderPluginThumbnail = function(plugin) {
			return $sce.trustAsHtml(plugin.img);
		};

		$scope.renderPluginDescription = function(plugin) {
			return $sce.trustAsHtml(plugin.description);
		};
		
		$scope.plugins_installed = <?php _e(json_encode($this->data['installed'])); ?>;
		$scope.issetAddonStatus =function( addon_status ) {
			return $scope.addon_status == addon_status;
		}
		$scope.setAddonStatus =function( addon_status ) {
			return $scope.addon_status = addon_status;
		}
		$scope.getAddonStatus =function() {
			return $scope.addon_status;
		}
		$scope.isShowAddon =function(addon_type) {
			if( $scope.addon_type!='all' && $scope.addon_type!= addon_type){
				return false
			}
			return true;
		}
	});
</script>
<style>
	.update-addon {
		display: inline-block;
		background-color: #d54e21;
		color: #fff;
		font-size: 9px;
		line-height: 17px;
		font-weight: 600;
		margin: 1px 0 0 2px;
		vertical-align: top;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		z-index: 26;
	}
	
	.addon-count {
		display: block;
		padding: 0 6px;
	}
	
	.plugin-card.update {
		border-color: red;
		background-color: #F5DDAA;
	}
	
</style>
<div>
  
  
</div>
<div class="wrap" ng-app="sortApp" ng-controller="mainController">
	<h1 style="text-align: center; font-size: 50px; padding: 50px;"><?php _e('WP Pipes\'s Addons','wp-pipes' );?></h1>
	<div class="wp-filter">
		<ul class="filter-links">
			<li class="plugin-install ng-class:{ 'current': issetAddonStatus(0) }"><a href ng-click="setAddonStatus(0)"><?php _e('Available','wp-pipes');?></a> </li>
			<li class="plugin-install ng-class:{ 'current': issetAddonStatus(1) }"><a href ng-click="setAddonStatus(1)"><?php _e('Installed','wp-pipes');?>
				<?php if($this->data['update_count']):?>
					<span class="update-addon"><span class="addon-count"><?php esc_html_e($this->data['update_count']);?></span></span>
				<?php endif;?></a></li>
							
		</ul>
		<form class="search-form search-plugins" method="get">
			<select ng-model="addon_type">
				<option value="all"><?php _e('Select Addon Type','wp-pipes');?></option>
				<option value="source"><?php _e('Source','wp-pipes');?></option>
				<option value="processor"><?php _e('Processor','wp-pipes');?></option>
				<option value="destination"><?php _e('Destination','wp-pipes');?></option>
			</select>
			<input type="hidden" name="tab" value="search">
					<label><span class="screen-reader-text">Search Plugins</span>
				<input type="search" name="s" value="" class="wp-filter-search" ng-model="searchPlugins" placeholder="Search Plugins">
			</label>
			<input type="submit" id="search-submit" class="button screen-reader-text" value="Search Plugins">	
		</form>
	</div>
	<br class="clear">
	<form id="plugin-filter" method="post">
		<input type="hidden" name="_wp_http_referer" value="/pipes/wp-admin/plugin-install.php?tab=popular">
		<div class="wp-list-table widefat plugin-install">
			<h2 class="screen-reader-text">Plugins list</h2>	
			<div id="the-list" ng-if="issetAddonStatus(0)">
				<div class="plugin-card" ng-repeat="plugin in plugins_avaiable | orderBy:sortType:sortReverse | filter:searchPlugins" ng-if="isShowAddon(plugin.addon_type)">
					<div class="plugin-card-top">
						<div class="name column-name" style="margin-right: 0;">
							<h3>
								<a href="{{ plugin.url }}" target="blank">
								{{ plugin.name }}
								<span class="plugin-icon" ng-bind-html="renderPluginThumbnail(plugin)">{{ plugin.img }}</span>
								</a>
							</h3>
						</div>
						<div class="desc column-description" ng-bind-html="renderPluginDescription(plugin)" style="margin-right: 0;">
							{{ plugin.description }}
						</div>
					</div>
					<div class="plugin-card-bottom">
						<div class="vers column-rating">
							<strong><?php _e('Addon type','wp-pipes');?>: {{ plugin.addon_type }}</strong>
						</div>
						<div class="column-updated">
							
						</div>
						<div class="column-downloaded">
							<strong><?php _e('Version','wp-pipes');?>: {{ plugin.last_version }}</strong></div>
						<div class="column-compatibility">
							
						</div>
					</div>
				</div>
			</div>
			<div id="the-list-installed" ng-if="issetAddonStatus(1)">
				<div ng-repeat="plugin in plugins_installed | orderBy:sortType:sortReverse | filter:searchPlugins" ng-if="isShowAddon(plugin.addon_type)" class="plugin-card ng-class:{'update':plugin.update}" >
					<div class="plugin-card-top">
						<div class="name column-name" style="margin-right: 0;">
							<h3>
								<a href="{{ plugin.url }}" target="blank" class="thickbox">
								{{ plugin.name }}
								<span class="plugin-icon" ng-bind-html="renderPluginThumbnail(plugin)">{{ plugin.img }}</span>
								</a>
							</h3>
						</div>
						<div class="desc column-description" ng-bind-html="renderPluginDescription(plugin)" style="margin-right: 0;">
							{{ plugin.description }}
							
						</div>
					</div>
					<div class="plugin-card-bottom">
						<div class="vers column-rating">
							<strong><?php _e('Addon type','wp-pipes');?>: {{ plugin.addon_type }}</strong>
						</div>
						<div class="column-updated">
							
						</div>
						<div class="column-downloaded">
							<strong><?php _e('Version','wp-pipes');?>: {{ plugin.version }}</strong></div>
						<div class="column-compatibility" ng-if="plugin.update=='1'">
							<a class="button-primary" href="{{ plugin.url }}"><?php _e('Update to');?>:  {{ plugin.last_version }}</a>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>