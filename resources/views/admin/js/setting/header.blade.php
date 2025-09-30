<script>
	onDocumentReady(function(event) {
		let headerFixedTopEl = document.querySelector("input[type=checkbox][name=fixed_top]");
		if (headerFixedTopEl) {
			toggleHeaderFixedTopFields(headerFixedTopEl, event.type);
			headerFixedTopEl.addEventListener("change", e => toggleHeaderFixedTopFields(e.target, e.type));
		}
	});
	
	function toggleHeaderFixedTopFields(headerFixedTopEl) {
		if (!headerFixedTopEl) return;
		
		let action = headerFixedTopEl.checked ? "show" : "hide";
		setElementsVisibility(action, ".fixed-header");
	}
</script>
