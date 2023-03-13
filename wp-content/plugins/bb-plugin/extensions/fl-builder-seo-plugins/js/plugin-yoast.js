/* global YoastSEO */

class BeaverBuilderYoast {
    constructor() {
       // Ensure YoastSEO.js is present and can access the necessary features.
       if ( typeof YoastSEO === "undefined" || typeof YoastSEO.analysis === "undefined" || typeof YoastSEO.analysis.worker === "undefined" ) {
            return;
        }

        YoastSEO.app.registerPlugin( "beaverPlugin", { status: "ready" } );

        this.registerModifications();
    }

    /**
     * Registers the addContent modification.
     *
     * @returns {void}
     */
    registerModifications() {
        const callback = this.addContent.bind( this );

        // Ensure that the additional data is being seen as a modification to the content.
        YoastSEO.app.registerModification( "content", callback, "beaverPlugin", 10 );
    }

    /**
     * Adds to the content to be analyzed by the analyzer.
     *
     * @param {string} data The current data string.
     *
     * @returns {string} The data string parameter with the added content.
     */
    addContent( data ) {
        return window.bb_seo_data.content ;
    }
}
/**
 * Adds eventlistener to load the plugin.
 */
if ( typeof YoastSEO !== "undefined" && typeof YoastSEO.app !== "undefined" ) {
  new BeaverBuilderYoast();
} else {
  jQuery( window ).on(
    "YoastSEO:ready",
    function() {
      new BeaverBuilderYoast();
    }
  );
}
