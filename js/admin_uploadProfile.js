document.getElementById('openProfileModal').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent the "#" link from jumping
    var myModal = new bootstrap.Modal(document.getElementById('profileModal'));
    myModal.show();
});

document.getElementById("profileForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("uploadProfile.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        let msg = document.getElementById("profileMsg");
        if (data.success) {
            msg.innerHTML = "<span class='text-success'>" + data.message + "</span>";

            // Update modal preview
            document.getElementById("currentProfilePreview").src = data.newImage + "?t=" + new Date().getTime();

            // Update top-right profile picture
            document.querySelector("#profileDropdown img.rounded-circle").src = data.newImage + "?t=" + new Date().getTime();

            // Close the modal after 0.5 seconds
            setTimeout(() => {
                let modalElement = document.getElementById('profileModal');
                let modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }, 500);

        } else {
            msg.innerHTML = "<span class='text-danger'>" + data.message + "</span>";
        }
    })
    .catch(() => {
        document.getElementById("profileMsg").innerHTML = "<span class='text-danger'>Upload failed.</span>";
    });
});
