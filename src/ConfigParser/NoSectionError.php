<?php
/*
 * Copyright (c) 2012, Martin Vondra.
 * All Rights Reserved.
 *
 * DESCRIPTION
 * config parser module
 * parsing configuration from standard ini file with section
 * [section]
 * option1 = value1
 * option2 = value2
 *
 * @author Martin Vondra <martin.vondra@email.cz>
 */

namespace Leolos\ConfigParser;


/**
 * NoSectionError
 * @author Martin Vondra <martin.vondra@email.cz>
 *
 */
class NoSectionError extends ConfigParserError {

}
