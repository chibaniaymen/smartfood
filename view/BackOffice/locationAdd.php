<?php include 'header.php'; ?>
<div class="page-header">
    <h3 class="fw-bold mb-3">Add New Location</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="index.php"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="locations.php?action=list">Locations</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Add</a></li>
    </ul>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Location Details</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form id="locationForm" action="locations.php?action=add" method="POST" novalidate>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                        <small id="nameErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" id="address" class="form-control">
                        <small id="addressErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" id="city" class="form-control">
                        <small id="cityErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" id="country" class="form-control" value="France">
                    </div>
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" name="capacity" id="capacity" class="form-control" min="0">
                        <small id="capacityErr" class="text-danger"></small>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-primary">Save Location</button>
                        <a href="locations.php?action=list" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="view/BackOffice/validationLocation.js"></script>
<?php include 'footer.php'; ?>
