(function($) {

	var RankMathIntegration = function() {
		this.init()
		this.hooks()
	}

	RankMathIntegration.prototype.init = function() {
		this.pluginName = 'rank-math-review-analysis'
		this.fields = {
			content: {
				'wp_review_desc': 'editor'
			}
		}
	}

	RankMathIntegration.prototype.hooks = function() {
		wp.hooks.addFilter('rank_math_content', this.pluginName, $.proxy(this.reviewDescription, this))
	}

	RankMathIntegration.prototype.reviewDescription = function(content) {
		return window.bb_seo_data.content;
	}

	$(document).ready( function() {
		new RankMathIntegration()
	})
})(jQuery);
