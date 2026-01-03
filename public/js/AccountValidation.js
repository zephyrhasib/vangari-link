document.addEventListener("DOMContentLoaded", function () {
  var profileForm = document.getElementById("profileForm");
  var passwordForm = document.getElementById("passwordForm");
  var deleteForm = document.getElementById("deleteForm");

  if (profileForm) {
    profileForm.addEventListener("submit", function (e) {
      var name = profileForm.querySelector("input[name='name']").value.trim();
      var area = profileForm.querySelector("select[name='area']").value.trim();
      var phone = profileForm.querySelector("input[name='phone']").value.trim();
      var email = profileForm.querySelector("input[name='email']").value.trim();

      if (name.length === 0) {
        alert("Name is required.");
        e.preventDefault();
        return;
      }

      var shopInput = profileForm.querySelector("input[name='shop_name']");
      if (shopInput && shopInput.value.trim() === "") {
      alert("Shop name is required.");
      e.preventDefault();
      return;
      }


      if (area === "Select Area") {
        alert("Please select a valid area.");
        e.preventDefault();
        return;
      }

      for (var i = 0; i < phone.length; i++) {
        var ch = phone[i];
        var ok = (ch >= "0" && ch <= "9") || ch === "+" || ch === "-" || ch === " ";
        if (!ok) {
          alert("Phone number contains invalid characters.");
          e.preventDefault();
          return;
        }
      }

      if (phone.length < 8) {
        alert("Phone number looks too short.");
        e.preventDefault();
        return;
      }

      if (email.indexOf("@") === -1 || email.indexOf(".") === -1) {
        alert("Please enter a valid email.");
        e.preventDefault();
        return;
      }
    });
  }

  if (passwordForm) {
    passwordForm.addEventListener("submit", function (e) {
      var currentPassword = passwordForm.querySelector("input[name='current_password']").value;
      var newPassword = passwordForm.querySelector("input[name='new_password']").value;
      var confirmPassword = passwordForm.querySelector("input[name='confirm_password']").value;

      if (newPassword.length < 8) {
        alert("New password must be at least 8 characters.");
        e.preventDefault();
        return;
      }

      if (newPassword !== confirmPassword) {
        alert("New password and confirm password do not match.");
        e.preventDefault();
        return;
      }

      if (newPassword === currentPassword) {
        alert("New password cannot be the same as current password.");
        e.preventDefault();
        return;
      }
    });
  }

  if (deleteForm) {
    deleteForm.addEventListener("submit", function (e) {
      var confirmDelete = deleteForm.querySelector("input[name='confirm_delete']").value.trim();

      if (confirmDelete !== "YES") {
        alert("Type YES to confirm deletion.");
        e.preventDefault();
        return;
      }

      var ok = confirm("Are you sure you want to delete your account?");
      if (!ok) {
        e.preventDefault();
        return;
      }
    });
  }
});
