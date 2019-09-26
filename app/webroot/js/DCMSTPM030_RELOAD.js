var refreshIntervalId


function initReload() {

	startReload()
}

function startReload() {

	refreshIntervalId = setInterval(reloading, 180000);
}

function reloading() {

	location.href= '/DCMS/TPM030/index';
}



