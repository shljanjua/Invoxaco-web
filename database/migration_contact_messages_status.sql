-- =========================================================
-- Invoxaco - Add missing status column to contact_messages
-- Run this directly against your production database (e.g. via
-- Hostinger phpMyAdmin) to fix the contact form.
--
-- Symptom this fixes: submitting the public Contact Us form shows
-- "Something went wrong. Our team has been notified." This happens
-- because ContactController::store() inserts a status column
-- ('new') on every submission, but the production contact_messages
-- table predates that column (it currently only has id, name, email,
-- subject, message, created_at) and was never migrated for it.
--
-- This script only ADDS a column and never drops or modifies
-- existing data, so it is safe to run on a live database with
-- existing rows.
-- =========================================================

SET NAMES utf8mb4;

ALTER TABLE contact_messages
  ADD COLUMN status ENUM('new','read','replied') NOT NULL DEFAULT 'new' AFTER message;
