<?php
/*
 *
 * Copyright (c) 2012, Martin Vondra.
 * All Rights Reserved.
 *
 * DESCRIPTION
 * Database comunication module
 * Create and hold a db connection
 * Manage transactions
 * Execute queries
 *
 * @author Martin Vondra <martin.vondra@email.cz>
 */

namespace Leolos\MysqlDb;

/**
 * DuplicateEntryError
 * indicates duplicate entry error - unique index fault
 * @author Martin Vondra <martin.vondra@email.cz>
 */
class DuplicateEntryError extends MysqlError {
}