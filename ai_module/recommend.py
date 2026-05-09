import sys, json, pickle
import pandas as pd
from sklearn.metrics.pairwise import cosine_similarity

# Load saved files
clf = pickle.load(open("C:/xammp/htdocs/careerproject/ai_module/career_model.pkl", "rb"))
vectorizer = pickle.load(open("C:/xammp/htdocs/careerproject/ai_module/vectorizer.pkl", "rb"))
data = pickle.load(open("C:/xammp/htdocs/careerproject/ai_module/career_data.pkl", "rb"))

def recommend(skills, interests):
    user_input = skills + " " + interests
    user_vec = vectorizer.transform([user_input])
    prediction = clf.predict(user_vec)[0]
    prob = clf.predict_proba(user_vec).max()

    tfidf_matrix = vectorizer.transform(data["combined"])
    cos_sim = cosine_similarity(user_vec, tfidf_matrix)[0]
    top_indices = cos_sim.argsort()[-10:][::-1]

    jobs = data.iloc[top_indices]["career_option"].tolist()
    scores = [round(cos_sim[i]*100,2) for i in top_indices]
    roadmaps = data.iloc[top_indices]["roadmap"].tolist()

    # ✅ Remove duplicate career options
    unique = {}
    for j, s, r in zip(jobs, scores, roadmaps):
        if j not in unique:
            unique[j] = {"score": s, "roadmap": r}

    jobs = list(unique.keys())
    scores = [unique[j]["score"] for j in jobs]
    roadmaps = [unique[j]["roadmap"] for j in jobs]

    predicted_roadmap = data.loc[data['career_option'] == prediction, 'roadmap'].values[0]

    return {
        "predicted_career": prediction,
        "probability": round(prob*100,2),
        "predicted_roadmap": predicted_roadmap,
        "jobs": jobs,
        "match_scores": scores,
        "roadmaps": roadmaps
    }

if __name__ == "__main__":
    skills = sys.argv[1] if len(sys.argv) > 1 else ""
    interests = sys.argv[2] if len(sys.argv) > 2 else ""
    result = recommend(skills, interests)
    print(json.dumps(result))
