<?php

/*
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * LibVapmPMMP provides asynchronous, promise, coroutine and thread utilities
 * for PocketMine-MP.
 * Original authors: VennDev, xqwtxon and xwertxy.
 * Maintained by ReinfyTeam.
 *
 * @link https://github.com/ReinfyTeam/LibVapmPMMP
 *
 *
 */

declare(strict_types=1);

namespace vennv\vapm;

final class Error {
	public const FAILED_IN_FETCHING_DATA = "Error in fetching data";

	public const WRONG_TYPE_WHEN_USE_CURL_EXEC = "curl_exec() should return string|false when CURL-OPT_RETURN-TRANSFER is set";

	public const UNABLE_START_THREAD = "Unable to start thread";

	public const DEFERRED_CALLBACK_MUST_RETURN_GENERATOR = "Deferred callback must return a Generator";

	public const UNABLE_TO_OPEN_FILE = "Error: Unable to open file!";

	public const FILE_DOES_NOT_EXIST = "Error: File does not exist!";

	public const FILE_ALREADY_EXISTS = "Error: File already exists!";

	public const CANNOT_FIND_FUNCTION_KEYWORD = "Cannot find function or fn keyword in closure";

	public const CANNOT_READ_FILE = "Cannot read file";

	public const INPUT_MUST_BE_STRING_OR_CALLABLE = "Input must be string or callable";

	public const ERROR_TO_CREATE_SOCKET = "Error to create socket";

	public const PAYLOAD_TOO_LARGE = "Payload too large";

	public const INVALID_ARRAY = "Invalid array";

	public const ASYNC_AWAIT_MUST_CALL_IN_ASYNC_FUNCTION = "Async::await() must call in async function";

	public const CHANNEL_IS_CLOSED = "Channel is closed";
}