<?php include 'header.php'; ?>
<div class="page-header">
    <h3 class="fw-bold mb-3">Edit Event</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="index.html"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="events.php?action=list">Events</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Edit</a></li>
    </ul>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Event Details</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form id="eventForm" action="events.php?action=edit&id=<?php echo $event->getId(); ?>" method="POST" novalidate>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($event->getTitle()); ?>">
                        <small id="titleErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($event->getDescription()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Event Date</label>
                        <input type="datetime-local" name="event_date" id="event_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($event->getEventDate())); ?>">
                        <small id="dateErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <select name="location_id" id="location_id" class="form-select">
                            <option value="">-- Select a Location --</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?php echo $loc['id']; ?>" <?php echo $event->getLocationId() == $loc['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($loc['name']); ?> (<?php echo htmlspecialchars($loc['city']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small id="locErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?php echo $event->getPrice(); ?>" min="0">
                        </div>
                        <small id="priceErr" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $event->getStatus() == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="cancelled" <?php echo $event->getStatus() == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="completed" <?php echo $event->getStatus() == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-primary">Update Event</button>
                        <a href="events.php?action=list" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="view/BackOffice/validationEvent.js"></script>
<?php include 'footer.php'; ?>
