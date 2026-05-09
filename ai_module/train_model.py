import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.ensemble import RandomForestClassifier
import pickle

# Load dataset
data = pd.read_csv("C:/xammp/htdocs/careerproject/ai_module/career_roles.csv")

# Clean dataset
data = data.dropna(subset=["skills", "interests", "career_option", "roadmap"])
data["skills"] = data["skills"].astype(str).str.strip()
data["interests"] = data["interests"].astype(str).str.strip()
data["career_option"] = data["career_option"].astype(str).str.strip()
data["roadmap"] = data["roadmap"].astype(str).str.strip()

if data.empty:
    raise ValueError("Dataset is empty after cleaning. Please check career_roles.csv")

# Combine skills + interests
data["combined"] = data["skills"] + " " + data["interests"]

# TF-IDF vectorization
vectorizer = TfidfVectorizer(stop_words="english")
X = vectorizer.fit_transform(data["combined"])
y = data["career_option"]

# Train model
clf = RandomForestClassifier(n_estimators=200, random_state=42)
clf.fit(X, y)

# Save models
pickle.dump(clf, open("C:/xammp/htdocs/careerproject/ai_module/career_model.pkl", "wb"))
pickle.dump(vectorizer, open("C:/xammp/htdocs/careerproject/ai_module/vectorizer.pkl", "wb"))
pickle.dump(data, open("C:/xammp/htdocs/careerproject/ai_module/career_data.pkl", "wb"))

print("✅ Model training complete with roadmap included!")
