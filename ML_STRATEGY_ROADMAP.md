# Murugo Platform: Machine Learning Strategy Roadmap

**Objective:** To create a comprehensive machine learning roadmap for enhancing platform integrity through fraud detection and listing quality assurance.

---

## Table of Contents

1.  [Executive Summary](#executive-summary)
2.  [Current Data Analysis](#current-data-analysis)
3.  [Recommended Additional Data Collection](#recommended-additional-data-collection)
4.  [Use Case 1: Fraudulent User Detection](#use-case-1-fraudulent-user-detection)
5.  [Use Case 2: Fake Listing Identification](#use-case-2-fake-listing-identification)
6.  [Other Potential ML Use Cases](#other-potential-ml-use-cases)
7.  [Data Pipeline and Implementation Approach](#data-pipeline-and-implementation-approach)
8.  [Success Metrics and Measurement](#success-metrics-and-measurement)

---

## 1. Executive Summary

This document outlines a strategic roadmap for integrating machine learning into the Murugo platform. The primary focus is on developing models for **fraudulent user detection** and **fake listing identification**. By leveraging existing data and collecting new, targeted data points, we can build a more secure and trustworthy marketplace for landlords and tenants.

The proposed approach involves a phased implementation, starting with rule-based systems and progressing to more complex models like anomaly detection and supervised classification. This will allow for incremental improvements while managing development resources effectively.

---

## 2. Current Data Analysis

The platform currently collects a wealth of data that can be immediately leveraged for machine learning. The key data sources are:

| Table                 | Relevant Fields for ML                                                                                                                                                              |
| --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `users`               | `id`, `created_at`, `role`, `is_verified`, `last_active_at`, `profile_completion_percentage`, `verification_status`, `phone_number`, `email`                                                |
| `properties`          | `id`, `landlord_id`, `created_at`, `status`, `price`, `location`, `bedrooms`, `bathrooms`, `description`, `title`, `views_count`, `latitude`, `longitude`                                  |
| `activity_logs`       | `user_id`, `action` (e.g., `property_created`), `ip_address`, `user_agent`, `created_at`                                                                                                  |
| `reports`             | `reporter_id`, `reported_user_id`, `reported_property_id`, `category` (e.g., `fraud`, `fake_listing`), `status`                                                                          |
| `reviews`             | `user_id`, `property_id`, `landlord_id`, `property_rating`, `landlord_rating`, `property_review`, `landlord_review`, `is_approved`                                                          |
| `messages`            | `sender_id`, `recipient_id`, `body`, `created_at`, `conversation_id`                                                                                                                  |
| `search_histories`    | `user_id`, `ip_address`, `search_query`, `filters`, `results_count`, `session_id`                                                                                                   |
| `images`              | `property_id`, `image_path`, `is_primary`                                                                                                                                           |

This data provides a strong foundation for building initial models based on user profiles, property details, and user activity.

---

## 3. Recommended Additional Data Collection

To enhance model accuracy, we recommend collecting the following additional data points:

| Data Point                          | Table/Method                                                                                                                                                                | Justification                                                                                                                                                                               |
| ----------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **User Behavior Tracking**          | New table: `user_events` (user_id, event_name, page_url, timestamp, details_json)                                                                                            | Tracks clicks, time on page, scroll depth. Helps differentiate human behavior from bots and identify unusual navigation patterns.                                                          |
| **Image Metadata**                  | Add fields to `images` table: `image_hash` (perceptual hash), `exif_data` (JSON), `image_size`, `resolution`                                                                | `image_hash` is crucial for detecting duplicate or near-duplicate images across listings. EXIF data can reveal if an image is a screenshot or downloaded from the internet.               |
| **Phone Number Verification**       | Add field to `users` table: `phone_verified_at` (timestamp)                                                                                                                 | Adds another layer of user verification, making it harder for fraudsters to create multiple accounts.                                                                                     |
| **IP Address Reputation**           | Integrate with a service like AbuseIPDB or MaxMind to get a risk score for each `ip_address` in `activity_logs`. Store this score in a new table or in the `activity_logs` table. | Quickly identifies users operating from known malicious IPs (proxies, VPNs, Tor nodes).                                                                                                   |
| **Listing Edit History**            | New table: `property_edits` (property_id, user_id, changed_fields, old_value, new_value, timestamp)                                                                         | Tracks changes to listings. Frequent, significant changes (e.g., price drops, location changes) can be a red flag.                                                                        |

---

## 4. Use Case 1: Fraudulent User Detection

**Objective:** Identify and flag users who are likely to be scammers or engaging in fraudulent activities.

### Model Recommendations

1.  **Phase 1: Rule-Based Heuristics & Scoring System**
    *   **Approach:** Start with a simple, interpretable scoring system based on a set of rules. This provides a baseline and immediate value.
    *   **Justification:** Fast to implement, easy to understand, and effective for catching obvious fraud.

2.  **Phase 2: Anomaly Detection**
    *   **Models:** Isolation Forest, Local Outlier Factor (LOF).
    *   **Approach:** Use unsupervised learning to identify users who deviate significantly from the norm without needing labeled fraud data.
    *   **Justification:** Excellent for finding new and unknown fraud patterns.

3.  **Phase 3: Supervised Classification (Long-term)**
    *   **Models:** Random Forest, Gradient Boosting (XGBoost, LightGBM).
    *   **Approach:** Once enough data has been collected and labeled (from `reports` and admin actions), train a supervised model to predict fraud with high accuracy.
    *   **Justification:** Provides the highest accuracy and can generalize well if the training data is diverse.

### Feature Engineering

-   **User Profile Features:**
    -   `profile_completion_percentage` (lower scores are riskier).
    -   `is_verified` (unverified users are riskier).
    -   `verification_status` (pending/rejected are riskier).
    -   Time between account creation and first property listing (very short time is a red flag).
    -   Use of generic or suspicious `business_name`.
-   **Activity Features:**
    -   Number of properties listed in a short time frame (e.g., >5 in one hour).
    -   Number of logins from different IP addresses or countries.
    -   `ip_address` reputation score.
    -   High rate of sending identical messages (`messages` table).
-   **Reputation Features:**
    -   Number of times the user has been reported (`reports` table).
    -   Average `landlord_rating` from `reviews`.

---

## 5. Use Case 2: Fake Listing Identification

**Objective:** Automatically detect and flag property listings that are fake, duplicates, or intentionally misleading.

### Model Recommendations

1.  **Phase 1: Content & Image Analysis**
    *   **Models:**
        -   **Text:** TF-IDF with Logistic Regression for text classification (e.g., generic vs. unique description).
        -   **Image:** Perceptual Hashing (e.g., pHash) for duplicate image detection.
    *   **Justification:** Addresses the most common fake listing patterns (copied text, stock photos) with relatively simple models.

2.  **Phase 2: Anomaly Detection on Listing Attributes**
    *   **Model:** Isolation Forest.
    *   **Approach:** Identify listings with unusual combinations of features (e.g., extremely low `price` for a prime `location` and high `bedrooms`).
    *   **Justification:** Catches listings that look plausible on the surface but are statistical outliers.

3.  **Phase 3: Supervised Classification & NLP**
    *   **Models:**
        -   **Classification:** Random Forest or Gradient Boosting on a combination of all features.
        -   **NLP:** Fine-tuning a pre-trained model like DistilBERT on property descriptions to detect subtle cues of fake listings.
    *   **Justification:** Provides the highest accuracy by combining all signals. BERT can understand the context and semantics of the description, catching more sophisticated fakes.

### Feature Engineering

-   **Content-Based Features:**
    -   `description` length (very short or very long can be suspicious).
    -   Presence of phone numbers or email addresses in the `description` (a common tactic to bypass the platform's messaging system).
    -   Sentiment analysis of the `description`.
    -   Percentage of text in ALL CAPS.
-   **Image-Based Features (requires new data collection):**
    -   Number of images per listing (too few is a red flag).
    -   `image_hash` to find duplicate images across the platform.
    -   `resolution` and `image_size` (very low-resolution images are often downloaded from the internet).
-   **Price & Attribute Features:**
    -   Price per square meter (if `size` is added) compared to the average for the `location`.
    -   `price` z-score (how many standard deviations from the mean price in that area).
-   **Landlord Features:**
    -   The landlord's fraud score (from Use Case 1).
    -   Number of active listings by the landlord.

---

## 6. Other Potential ML Use Cases

-   **Property Price Estimation:**
    -   **Model:** Gradient Boosting Regressor (e.g., XGBoost).
    -   **Data:** `price`, `location`, `bedrooms`, `bathrooms`, `amenities`, `size` (new field).
    -   **Benefit:** Provide users with estimated market rates, helping them identify overpriced or underpriced listings.
-   **Personalized Recommendations:**
    -   **Model:** Collaborative Filtering or Content-Based Filtering.
    -   **Data:** `search_histories`, `favorites`, `property_comparisons`, user `preferences`.
    -   **Benefit:** Improve user engagement by showing them properties they are most likely to be interested in.
-   **Review Spam Detection:**
    -   **Model:** NLP classification model (e.g., Naive Bayes on text features).
    -   **Data:** `reviews` table (`property_review`, `landlord_review`).
    -   **Benefit:** Ensure the integrity of the review system by filtering out fake or malicious reviews.

---

## 7. Data Pipeline and Implementation Approach

We recommend a phased, iterative approach to implementation:

1.  **Data Collection & Enhancement (Month 1-2):**
    -   Implement the recommended additional data collection points (especially `image_hash` and `user_events`).
    -   Set up a dedicated database or schema for ML-related data and model outputs.

2.  **Phase 1: Heuristics and Basic Models (Month 2-3):**
    -   **Pipeline:** Create nightly batch jobs (Laravel Scheduled Jobs) that run scripts to:
        -   Calculate fraud scores based on rules.
        -   Generate image hashes and check for duplicates.
        -   Flag listings with suspicious text patterns.
    -   **Output:** Store the scores and flags in new columns in the `users` and `properties` tables (e.g., `fraud_score`, `listing_quality_score`).

3.  **Phase 2: Anomaly Detection (Month 4-6):**
    -   **Pipeline:**
        -   Export feature-engineered data to a CSV or Parquet file.
        -   Train an Isolation Forest model in a Python environment (using libraries like Scikit-learn).
        -   Save the trained model.
        -   Run a batch prediction job daily to update anomaly scores.

4.  **Phase 3: Supervised Learning (Month 7+):**
    -   **Pipeline:**
        -   Continuously collect and label data (e.g., when an admin confirms a listing is fake, a `is_fake` flag is set).
        -   Set up a proper ML training pipeline (e.g., using Kubeflow, MLflow, or a simpler custom solution) to periodically retrain the models on new data.
        -   Deploy the model as a microservice or integrate it directly into the Laravel application for real-time predictions if needed.

---

## 8. Success Metrics and Measurement

| Use Case                      | Primary Metric                                                                                                                                                                      | Secondary Metrics                                                                                                                                                                           |
| ----------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Fraudulent User Detection** | **Precision & Recall** of the fraud model. (Precision: Of all users flagged as fraudulent, how many actually were? Recall: Of all actual fraudsters, how many did we catch?) | -   Reduction in the number of user-submitted `reports` with the category `fraud`.
-   Manual review time saved by admins.                                                          |
| **Fake Listing Identification** | **Precision & Recall** of the fake listing model.                                                                                                                                   | -   Reduction in the number of `reports` with the category `fake_listing`.
-   Increase in user trust scores or positive feedback.
-   Decrease in the average time a fake listing stays on the platform. |

### Measurement Strategy

-   **A/B Testing:** When deploying a new model, run it in shadow mode first (making predictions without taking action) to monitor its performance. Then, roll it out to a small percentage of users/listings and compare the results against a control group.
-   **Admin Dashboard:** Create a dashboard for admins to review flagged users and listings. This dashboard should allow them to confirm or deny the model's prediction, providing crucial labeled data for retraining and improving the models over time.
-   **Regular Reporting:** Generate monthly reports on model performance, the number of fraudulent accounts/listings detected, and the impact on user reports and admin workload.
